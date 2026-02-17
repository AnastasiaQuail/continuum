<?php

declare(strict_types=1);

namespace Continuum\Service\Database;

use Continuum\Dto\Response\Admin\Database\BackupFile;
use Continuum\Dto\Response\Admin\Database\DatabaseCredentials;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use RuntimeException;
use SensitiveParameter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final readonly class DatabaseDumper
{
    public function __construct(
        #[SensitiveParameter]
        #[Autowire(env: 'DATABASE_URL')]
        private string $databaseUrl,
        #[Autowire('%kernel.project_dir%/var/backups')]
        private string $backupDir,
        private LoggerInterface $logger,
        private DatabaseDumpCache $cache,
    ) {}

    /**
     * @return list<BackupFile>
     */
    public function getBackups(): array
    {
        try {
            $finder = $this->getFinder();
        } catch (DirectoryNotFoundException) {
            return [];
        }

        $backups = [];

        foreach ($finder as $file) {
            $size = $file->getSize();
            $date = $file->getMTime();

            $backups[] = new BackupFile(
                name: $file->getFilename(),
                size: false !== $size ? $size : 0,
                date: new DateTimeImmutable(sprintf('@%d', false !== $date ? $date : 0)),
            );
        }

        return $backups;
    }

    public function hasRelevantBackup(): bool
    {
        $lastBackupTime = $this->cache->get();

        if (null === $lastBackupTime) {
            $this->cache->save($lastBackupTime = $this->getLastBackupTime());
        }

        return $lastBackupTime > new DateTimeImmutable('-1 day');
    }

    /**
     * @throws RuntimeException
     */
    public function makeBackup(): string
    {
        try {
            $backupPath = $this->doBackup();
        } catch (RuntimeException $exception) {
            $this->logger->error($exception->getMessage());

            if ($exception instanceof ProcessFailedException) {
                throw new RuntimeException('Backup failed', $exception->getCode(), previous: $exception);
            }

            throw $exception;
        }

        $this->cache->save(new DateTimeImmutable('now'));

        $this->clearLegacyDumps();

        return $backupPath;
    }

    /**
     * @throws RuntimeException
     */
    public function makeRestore(string $backupFile): void
    {
        try {
            $this->doRestore($backupFile);
        } catch (RuntimeException $exception) {
            $this->logger->error($exception->getMessage());

            if ($exception instanceof ProcessFailedException) {
                throw new RuntimeException('Backup failed', $exception->getCode(), previous: $exception);
            }

            throw $exception;
        }
    }

    private function getFinder(): Finder
    {
        return new Finder()->files()->sortByModifiedTime()
            ->in($this->backupDir)
            ->name('*.sql');
    }

    private function getLastBackupTime(): ?DateTimeImmutable
    {
        try {
            $finder = $this->getFinder()->sortByModifiedTime();
        } catch (DirectoryNotFoundException) {
            return null;
        }

        /** @var SplFileInfo $file */
        $file = $finder->getIterator()->current();

        $time = $file->getCTime();
        if (false === $time) {
            $time = $file->getMTime();
        }

        if (false === $time) {
            throw new RuntimeException('Unable to retrieve last backup time');
        }

        return DateTimeImmutable::createFromTimestamp($time);
    }

    /**
     * @throws RuntimeException
     */
    private function doBackup(): string
    {
        if (!is_dir($this->backupDir) && !mkdir($this->backupDir, 0o755, true) && !is_dir($this->backupDir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $this->backupDir));
        }

        $db = $this->getCredentials();
        $backupPath = sprintf('%s/%s_%s.sql', $this->backupDir, $db->name, date('Y-m-d_H-i-s'));

        $this->logger->info('Running dump backup');

        $command = [
            'pg_dump',
            '--format=plain',
            '-h',
            $db->host,
            '-p',
            $db->port,
            '-U',
            $db->user,
            '-f',
            $backupPath,
            $db->name,
        ];

        new Process($command)
            ->setEnv(['PGPASSWORD' => $db->password])
            ->setTimeout(300)
            ->mustRun();

        if (!file_exists($backupPath)) {
            throw new RuntimeException('Backup file was not created');
        }

        /** @var int<0, max> $filesize */
        $filesize = filesize($backupPath);
        $this->logger->notice(
            sprintf('Backup created: %s (%s MB)', $backupPath, number_format($filesize / 1024 / 1024, 2))
        );

        return $backupPath;
    }

    /**
     * @throws RuntimeException
     */
    private function doRestore(string $backupFile): void
    {
        $db = $this->getCredentials();
        $backupPath = sprintf('%s/%s', $this->backupDir, $backupFile);

        if (!file_exists($backupPath)) {
            throw new RuntimeException('Backup file was not exists');
        }

        $this->logger->info('Running drop database');

        new Process(['dropdb', '--force', '-h', $db->host, '-p', $db->port, '-U', $db->user, $db->name])
            ->setEnv(['PGPASSWORD' => $db->password])
            ->setTimeout(150)
            ->mustRun();

        $this->logger->info('Running create database');

        new Process(['createdb', '-h', $db->host, '-p', $db->port, '-U', $db->user, $db->name])
            ->setEnv(['PGPASSWORD' => $db->password])
            ->setTimeout(150)
            ->mustRun();

        $this->logger->info('Running restore backup');

        new Process(['psql', '-h', $db->host, '-p', $db->port, '-U', $db->user, '-f', $backupPath, '-d', $db->name])
            ->setEnv(['PGPASSWORD' => $db->password])
            ->setTimeout(300)
            ->mustRun();

        $this->logger->notice('Backup restored');
    }

    private function getCredentials(): DatabaseCredentials
    {
        /** @var array{host: string, port: int<0, 65535>, user: string, pass: string, path: string} $parts */
        $parts = parse_url($this->databaseUrl);

        ['host' => $host, 'port' => $port, 'user' => $user, 'pass' => $password, 'path' => $dbName] = $parts;

        return new DatabaseCredentials(
            user: $user,
            password: $password,
            host: $host,
            port: $port,
            name: ltrim($dbName, '/'),
        );
    }

    private function clearLegacyDumps(): void
    {
        $removedFiles = 0;

        /** @var list<non-empty-string> $files */
        $files = glob($this->backupDir . '/*.sql', GLOB_ERR);

        foreach ($files as $file) {
            if (filemtime($file) < strtotime('-7 days')) {
                unlink($file);
                ++$removedFiles;
            }
        }

        if ($removedFiles > 0) {
            $this->logger->info(sprintf('Removed %d files', $removedFiles));
        }
    }
}
