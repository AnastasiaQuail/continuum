<?php

declare(strict_types=1);

namespace Continuum\Command;

use Continuum\Service\Database\DatabaseDumper;
use RuntimeException;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:db:restore',
    description: 'Restore database from backup',
)]
final readonly class DatabaseRestoreCommand
{
    public function __construct(
        private DatabaseDumper $databaseDumper,
    ) {}

    public function __invoke(SymfonyStyle $io, #[Argument] string $backupFile): int
    {
        try {
            $this->databaseDumper->makeRestore($backupFile);
        } catch (RuntimeException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
