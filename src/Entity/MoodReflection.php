<?php

declare(strict_types=1);

namespace Continuum\Entity;

use Continuum\Enum\CalendarEventType;
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
    private Uuid $id;

    #[ORM\Column(enumType: MoodType::class)]
    private MoodType $type = MoodType::Okay;

    #[ORM\Column(length: 255)]
    private string $text = '';

    public function __construct(
        #[ORM\Column(type: Types::DATE_IMMUTABLE)]
        private readonly DateTimeImmutable $date,
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

    public function getType(): MoodType
    {
        return $this->type;
    }

    public function setType(MoodType $type): void
    {
        $this->type = $type;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }
}
