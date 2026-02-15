<?php

declare(strict_types=1);

namespace Continuum\Entity;

use Continuum\Repository\WeeklyReflectionRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
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
    public private(set) Uuid $id;

    #[ORM\Column(type: Types::BOOLEAN)]
    public bool $isJoyPrivate = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    public bool $isDifficultyPrivate = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    public bool $isAchievementPrivate = false;

    #[ORM\Column(type: Types::TEXT, options: ['default' => ''])]
    public string $joyText = '';

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    public bool $joyIsPrivate = false;

    #[ORM\Column(type: Types::TEXT, options: ['default' => ''])]
    public string $difficultyText = '';

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    public bool $difficultyIsPrivate = false;

    #[ORM\Column(type: Types::TEXT, options: ['default' => ''])]
    public string $achievementText = '';

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    public bool $achievementIsPrivate = false;

    public function __construct(
        #[ORM\Column(type: Types::DATE_IMMUTABLE)]
        public private(set) readonly DateTimeImmutable $date,
        #[ORM\Column(type: Types::TEXT)]
        public string $joy {
            set => '' !== $value ? $value : throw new InvalidArgumentException('Joy cannot be empty.');
        },
        #[ORM\Column(type: Types::TEXT)]
        public string $difficulty {
            set => '' !== $value ? $value : throw new InvalidArgumentException('Difficulty cannot be empty.');
        },
        #[ORM\Column(type: Types::TEXT)]
        public string $achievement {
            set => '' !== $value ? $value : throw new InvalidArgumentException('Achievement cannot be empty.');
        },
    ) {
        $this->id = Uuid::v7();
    }
}
