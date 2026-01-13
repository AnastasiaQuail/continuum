<?php

declare(strict_types=1);

namespace Continuum\Command;

use Continuum\Entity\MoodReflection;
use Continuum\Entity\ReflectionMood;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:temp:mood-reflections',
    description: 'Migrate old ReflectionMood to new MoodReflection',
)]
final readonly class TempMoodReflectionsCommand
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function __invoke(SymfonyStyle $io): int
    {
        foreach ($this->entityManager->getRepository(ReflectionMood::class)->findAll() as $reflectionMood) {
            $moodReflection = new MoodReflection($reflectionMood->getDate());
            $moodReflection->setType($reflectionMood->getType());
            $moodReflection->setText($reflectionMood->getText());

            $this->entityManager->persist($moodReflection);
        }

        $this->entityManager->flush();

        $io->success('All mood reflection have been migrated.');

        return Command::SUCCESS;
    }
}
