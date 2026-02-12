<?php

declare(strict_types=1);

namespace Continuum\Entity;

use Continuum\Enum\CalendarEventFormat;
use Continuum\Enum\CalendarEventType;
use Continuum\Repository\CalendarEventRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CalendarEventRepository::class)]
#[ORM\Table(name: 'calendar_events')]
final class CalendarEvent
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    public function __construct(
        #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
        private readonly DateTimeImmutable $datetime,
        #[ORM\Column(enumType: CalendarEventFormat::class)]
        private readonly CalendarEventFormat $format,
        #[ORM\Column(enumType: CalendarEventType::class)]
        private readonly CalendarEventType $type,
        #[ORM\Column(length: 255)]
        private readonly string $title,
    ) {
        $this->id = Uuid::v7();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getDatetime(): DateTimeImmutable
    {
        return $this->datetime;
    }

    public function isAllDay(): bool
    {
        return CalendarEventFormat::Day === $this->format;
    }

    public function getFormat(): CalendarEventFormat
    {
        return $this->format;
    }

    public function getType(): CalendarEventType
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
