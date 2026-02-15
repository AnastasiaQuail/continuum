<?php

declare(strict_types=1);

namespace Continuum\Command;

use Continuum\Entity\WeeklyReflection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(
    name: 'app:migrate:weekly-reflections',
    description: 'Migrate old columns of weekly reflections',
)]
final readonly class TempWeeklyReflectionMigrationCommand
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function __invoke(): int
    {
        $dql = 'UPDATE ' . WeeklyReflection::class . ' wr '
            . 'SET wr.joyText = wr.joy, wr.joyIsPrivate = wr.isJoyPrivate, '
            . 'wr.difficultyText = wr.difficulty, wr.difficultyIsPrivate = wr.isDifficultyPrivate, '
            . 'wr.achievementText = wr.achievement, wr.achievementIsPrivate = wr.isAchievementPrivate';

        $this->entityManager->createQuery($dql)
            ->execute();

        return Command::SUCCESS;
    }
}
