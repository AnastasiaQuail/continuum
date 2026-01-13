<?php

declare(strict_types=1);

namespace Continuum\Entity;

use Continuum\Repository\WeeklyReflectionRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: WeeklyReflectionRepository::class)]
#[ORM\Table(name: 'weekly_reflections')]
#[ORM\UniqueConstraint(name: 'UNIQ_WEEKLY_REFLECTIONS_DATE', fields: ['date'])]
final class WeeklyReflection
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\Column(type: Types::TEXT)]
    private string $joy = '';

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isJoyPrivate = false;

    #[ORM\Column(type: Types::TEXT)]
    private string $difficulty = '';

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isDifficultyPrivate = false;

    #[ORM\Column(type: Types::TEXT)]
    private string $achievement = '';

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isAchievementPrivate = false;

    public function __construct(
        #[ORM\Column(type: Types::DATE_IMMUTABLE)]
        private DateTimeImmutable $date,
    ) {
        $this->id = Uuid::v7();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getJoy(): string
    {
        return $this->joy;
    }

    public function setJoy(string $joy): void
    {
        $this->joy = $joy;
    }

    public function isJoyPrivate(): bool
    {
        return $this->isJoyPrivate;
    }

    public function setIsJoyPrivate(bool $isJoyPrivate): void
    {
        $this->isJoyPrivate = $isJoyPrivate;
    }

    public function getDifficulty(): string
    {
        return $this->difficulty;
    }

    public function setDifficulty(string $difficulty): void
    {
        $this->difficulty = $difficulty;
    }

    public function isDifficultyPrivate(): bool
    {
        return $this->isDifficultyPrivate;
    }

    public function setIsDifficultyPrivate(bool $isDifficultyPrivate): void
    {
        $this->isDifficultyPrivate = $isDifficultyPrivate;
    }

    public function getAchievement(): string
    {
        return $this->achievement;
    }

    public function setAchievement(string $achievement): void
    {
        $this->achievement = $achievement;
    }

    public function isAchievementPrivate(): bool
    {
        return $this->isAchievementPrivate;
    }

    public function setIsAchievementPrivate(bool $isAchievementPrivate): void
    {
        $this->isAchievementPrivate = $isAchievementPrivate;
    }
}
