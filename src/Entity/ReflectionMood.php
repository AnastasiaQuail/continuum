<?php

declare(strict_types=1);

namespace Continuum\Entity;

use Continuum\Enum\CalendarEventType;
use Continuum\Enum\MoodType;
use Continuum\Repository\ReflectionMoodRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ReflectionMoodRepository::class)]
#[ORM\Table(name: 'reflection_moods')]
final class ReflectionMood
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    public function __construct(
        #[ORM\Column(type: Types::DATE_IMMUTABLE)]
        private readonly DateTimeImmutable $date,

        #[ORM\Column(enumType: MoodType::class)]
        private MoodType $type,

        #[ORM\Column(length: 255)]
        private string $text = '',
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

    public function getCalendarEventType(): CalendarEventType
    {
        return $this->type->toCalendarEventType();
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
