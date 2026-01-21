<?php

declare(strict_types=1);

namespace Continuum\Command;

use Continuum\Service\DatabaseDumper;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:db:backup',
    description: 'Create database backup using pg_dump',
)]
final readonly class DatabaseBackupCommand
{
    public function __construct(
        private DatabaseDumper $databaseDumper,
    ) {}

    public function __invoke(SymfonyStyle $io): int
    {
        try {
            $this->databaseDumper->makeBackup();
        } catch (RuntimeException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
