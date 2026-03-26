<?php

declare(strict_types=1);

namespace Continuum\Tests\Entity;

use Continuum\Entity\CalendarEvent;
use Continuum\Entity\User;
use Continuum\Enum\CalendarEventFormat;
use Continuum\Enum\CalendarEventType;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

#[CoversClass(CalendarEvent::class)]
final class CalendarEventTest extends TestCase
{
    public function testCreate(): void
    {
        $user = new User('email@example.com');
        $user->timezone = new DateTimeZone('Africa/Tunis');

        $calendarEvent = new CalendarEvent(
            datetime: new DateTimeImmutable('2020-01-01 00:00:00'),
            format: CalendarEventFormat::Day,
            type: CalendarEventType::Blue,
            title: 'example Name',
        );

        self::assertInstanceOf(UuidV7::class, $calendarEvent->id);
        self::assertSame('2020-01-01 00:00:00', $calendarEvent->datetime->format('Y-m-d H:i:s'));
        self::assertSame(CalendarEventType::Blue, $calendarEvent->type);
        self::assertSame('Example Name', $calendarEvent->title);
        self::assertSame('Africa/Tunis', $calendarEvent->getUserDatetime($user)->getTimezone()->getName());
        self::assertSame('2020-01-01 00:00:00', $calendarEvent->getUserDatetime($user)->format('Y-m-d H:i:s'));
        self::assertTrue($calendarEvent->isAllDay());
    }

    public function testCreatedAt(): void
    {
        $id = Uuid::v7();
        $calendarEvent = new CalendarEvent(
            datetime: new DateTimeImmutable(),
            format: CalendarEventFormat::Day,
            type: CalendarEventType::Blue,
            title: 'example Name',
        );
        new ReflectionProperty(CalendarEvent::class, 'id')->setValue($calendarEvent, $id);

        self::assertInstanceOf(UuidV7::class, $calendarEvent->id);
        self::assertSame($id->getDateTime()->getTimestamp(), $calendarEvent->getCreatedAt()->getTimestamp());
    }

    public function testEmptyTitle(): void
    {
        $this->expectExceptionObject(new InvalidArgumentException('Title cannot be empty.'));

        $calendarEvent = new CalendarEvent(
            datetime: new DateTimeImmutable(),
            format: CalendarEventFormat::Day,
            type: CalendarEventType::Blue,
            title: '',
        );

        self::assertSame('--- this assert only for phpstorm ---', $calendarEvent->title);
    }

    public function testFormat(): void
    {
        $user = new User('email@example.com');
        $user->timezone = new DateTimeZone('US/Michigan');

        $calendarEvent = new CalendarEvent(
            datetime: new DateTimeImmutable('2020-01-01 00:00:00'),
            format: CalendarEventFormat::Hour,
            type: CalendarEventType::Red,
            title: 'example',
        );

        self::assertSame('US/Michigan', $calendarEvent->getUserDatetime($user)->getTimezone()->getName());
        self::assertSame('2019-12-31 19:00:00', $calendarEvent->getUserDatetime($user)->format('Y-m-d H:i:s'));
    }
}
