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
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final readonly class DatabaseDumper
{
    public const string CACHE_KEY = 'app.db.backup_last_file';

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
            $finder = new Finder()->files()->sortByModifiedTime()->in($this->backupDir);
        } catch (DirectoryNotFoundException) {
            return [];
        }

        $backups = [];

        foreach ($finder->name('*.sql') as $file) {
            $backups[] = new BackupFile(
                name: $file->getFilename(),
                size: $file->getSize(),
                date: new DateTimeImmutable(sprintf('@%d', $file->getMTime())),
            );
        }

        return $backups;
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
                throw new RuntimeException('Backup failed');
            }

            throw $exception;
        }

        $this->cache->save($backupPath);

        $this->clearLegacyDumps();

        return $backupPath;
    }

    /**
     * @throws RuntimeException
     */
    private function doBackup(): string
    {
        if (!is_dir($this->backupDir) && !mkdir($this->backupDir, 0755, true) && !is_dir($this->backupDir)) {
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

        $this->logger->notice(
            sprintf(
                'Backup created: %s (%s MB)',
                $backupPath,
                number_format(filesize($backupPath) / 1024 / 1024, 2)
            )
        );

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
                throw new RuntimeException('Backup failed');
            }

            throw $exception;
        }
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
        $parts = parse_url($this->databaseUrl);

        if (!is_array($parts)) {
            throw new RuntimeException('Invalid database url');
        }

        ['user' => $user, 'pass' => $password, 'host' => $host, 'port' => $port, 'path' => $dbName] = $parts;
        $dbName = ltrim($dbName ?? '', '/');

        if (!$user || !$dbName) {
            throw new RuntimeException('Invalid database url components');
        }

        return new DatabaseCredentials(
            user: $user,
            password: $password,
            host: $host,
            port: (int) $port,
            name: $dbName,
        );
    }

    private function clearLegacyDumps(): void
    {
        $removedFiles = 0;

        foreach (glob($this->backupDir . '/*.sql') as $file) {
            if (filemtime($file) < strtotime('-7 days')) {
                unlink($file);
                $removedFiles++;
            }
        }

        if ($removedFiles > 0) {
            $this->logger->info(sprintf('Removed %d files', $removedFiles));
        }
    }
}
