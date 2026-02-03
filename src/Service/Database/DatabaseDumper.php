<?php

declare(strict_types=1);

namespace Continuum\Service\Database;

use Continuum\Dto\Response\Admin\Database\BackupFile;
use DateTime;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

final readonly class DatabaseDumper
{
    public const string CACHE_KEY = 'app.db.backup_last_file';

    public function __construct(
        #[Autowire(env: 'DATABASE_URL')]
        private string $databaseUrl,
        #[Autowire('%kernel.project_dir%/data/backups')]
        private string $backupDir,
        private LoggerInterface $logger,
        private DatabaseDumpCache $cache,
    ) {}

    /**
     * @return list<BackupFile>
     */
    public function getBackups(): array
    {
        $backups = [];
        $finder = new Finder()->files()->sortByModifiedTime()
            ->in($this->backupDir)
            ->name('*.sql');

        foreach ($finder as $file) {
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
        $parts = parse_url($this->databaseUrl);

        if (!is_array($parts)) {
            throw new RuntimeException('Invalid database url');
        }

        ['user' => $user, 'pass' => $password, 'host' => $host, 'port' => $port, 'path' => $dbName] = $parts;
        $dbName = ltrim($dbName ?? '', '/');

        if (!$user || !$dbName) {
            throw new RuntimeException('Invalid database url components');
        }

        if (!is_dir($this->backupDir) && !mkdir($this->backupDir, 0755, true) && !is_dir($this->backupDir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $this->backupDir));
        }

        $backupPath = sprintf('%s/%s_%s.sql', $this->backupDir, $dbName, date('Y-m-d_H-i-s'));

        $process = new Process(
            command: [
                'pg_dump',
                '--format=plain',
                '-h',
                $host,
                '-p',
                (string) $port,
                '-U',
                $user,
                '-f',
                $backupPath,
                $dbName,
            ],
            env: ['PGPASSWORD' => $password],
            timeout: 300,
        );

        $this->logger->notice('Running pg_dump…');

        $process->run();

        if (!$process->isSuccessful()) {
            $this->logger->error($process->getErrorOutput());

            throw new RuntimeException('Backup failed');
        }

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

    private function clearLegacyDumps(): void
    {
        $removedFiles = 0;

        foreach (glob($this->backupDir . '/*.sql') as $file) {
            if (filemtime($file) < strtotime('-14 days')) {
                unlink($file);
                $removedFiles++;
            }
        }

        if ($removedFiles > 0) {
            $this->logger->info(sprintf('Removed %d files', $removedFiles));
        }
    }
}
