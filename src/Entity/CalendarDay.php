<?php

declare(strict_types=1);

namespace Continuum\Entity;

use Continuum\Enum\CalendarDayType;
use Continuum\Repository\CalendarDayRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CalendarDayRepository::class)]
#[ORM\Table(name: 'calendar_days')]
final class CalendarDay
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    public function __construct(
        #[ORM\Column(type: Types::DATE_IMMUTABLE)]
        private readonly DateTimeImmutable $date,

        #[ORM\Column(enumType: CalendarDayType::class)]
        private readonly CalendarDayType $type,

        #[ORM\Column(length: 255)]
        private readonly string $title,

        #[ORM\Column(type: Types::TIME_IMMUTABLE, nullable: true)]
        private readonly ?DateTimeImmutable $time = null,
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

    public function getType(): CalendarDayType
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function isEvent(): bool
    {
        return $this->time !== null;
    }

    public function getTime(): DateTimeImmutable
    {
        return $this->time ?? throw new LogicException('There is no corresponding time for this day.');
    }
}
