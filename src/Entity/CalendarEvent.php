<?php

declare(strict_types=1);

namespace Continuum\Entity;

use Continuum\Enum\CalendarEventFormat;
use Continuum\Enum\CalendarEventType;
use Continuum\Repository\CalendarEventRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

#[ORM\Entity(repositoryClass: CalendarEventRepository::class)]
#[ORM\Table(name: 'calendar_events')]
final class CalendarEvent
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    public private(set) Uuid $id;

    public function __construct(
        #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
        public readonly DateTimeImmutable $datetime,
        #[ORM\Column(enumType: CalendarEventFormat::class)]
        private readonly CalendarEventFormat $format,
        #[ORM\Column(enumType: CalendarEventType::class)]
        public readonly CalendarEventType $type,
        #[ORM\Column(length: 255)]
        public private(set) string $title {
            set => '' !== $value ? mb_ucfirst($value) : throw new InvalidArgumentException('Title cannot be empty.');
        },
    ) {
        $this->id = Uuid::v7();
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        /** @var UuidV7 $id */
        $id = $this->id;

        return $id->getDateTime();
    }

    public function getUserDatetime(User $user): DateTimeImmutable
    {
        if ($this->isAllDay()) {
            return new DateTimeImmutable($this->datetime->format('Y-m-d H:i:s'), $user->timezone);
        }

        return $this->datetime->setTimezone($user->timezone);
    }

    public function isAllDay(): bool
    {
        return CalendarEventFormat::Day === $this->format;
    }
}
