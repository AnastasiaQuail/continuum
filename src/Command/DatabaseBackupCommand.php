<?php

declare(strict_types=1);

namespace Continuum\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'app:db:backup',
    description: 'Create database backup using pg_dump',
)]
final readonly class DatabaseBackupCommand
{
    public function __construct(
        #[Autowire(env: 'DATABASE_URL')]
        private string $databaseUrl,
        #[Autowire('%kernel.project_dir%/var/backups')]
        private string $backupDir,
    ) {}

    public function __invoke(
        SymfonyStyle $io,
    ): int {
        $parts = parse_url($this->databaseUrl);

        if (!is_array($parts)) {
            $io->error('Invalid database url');

            return Command::FAILURE;
        }

        ['user' => $user, 'pass' => $password, 'host' => $host, 'port' => $port, 'path' => $dbName] = $parts;
        $dbName = ltrim($dbName ?? '', '/');

        if (!$user || !$dbName) {
            $io->error('Invalid database url components');

            return Command::FAILURE;
        }

        if (!is_dir($this->backupDir) && !mkdir($this->backupDir, 0755, true) && !is_dir($this->backupDir)) {
            $io->error(sprintf('Directory "%s" was not created', $this->backupDir));

            return Command::FAILURE;
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

        $io->text('Running pg_dump…');

        $process->run();

        if (!$process->isSuccessful()) {
            $io->error('Backup failed');
            $io->error($process->getErrorOutput());

            return Command::FAILURE;
        }

        if (!file_exists($backupPath)) {
            $io->error('Backup file was not created');

            return Command::FAILURE;
        }

        $io->success(
            sprintf(
                'Backup created: %s (%s MB)',
                $backupPath,
                number_format(filesize($backupPath) / 1024 / 1024, 2)
            )
        );

        $removedFiles = 0;

        foreach (glob($this->backupDir . '/*.sql') as $file) {
            if (filemtime($file) < strtotime('-14 days')) {
                unlink($file);
                $removedFiles++;
            }
        }

        if ($removedFiles > 0) {
            $io->info(sprintf('Removed %d files', $removedFiles));
        }

        return Command::SUCCESS;
    }
}
