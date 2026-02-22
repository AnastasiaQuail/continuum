<?php

declare(strict_types=1);

namespace Continuum\Entity;

use Continuum\Enum\MoodType;
use Continuum\Repository\MoodReflectionRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: MoodReflectionRepository::class)]
#[ORM\Table(name: 'mood_reflections')]
#[ORM\UniqueConstraint(name: 'UNIQ_MOOD_REFLECTION_DATE', fields: ['date'])]
final class MoodReflection
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    public private(set) Uuid $id;

    #[ORM\Column(enumType: MoodType::class)]
    public MoodType $type = MoodType::Okay;

    #[ORM\Column(length: 255)]
    public string $text = '';

    public function __construct(
        #[ORM\Column(type: Types::DATE_IMMUTABLE)]
        public readonly DateTimeImmutable $date,
    ) {
        $this->id = Uuid::v7();
    }
}
