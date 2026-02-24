<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Response\Calendar;

use Continuum\Dto\Response\Calendar\CombinedCalendarEvent;
use Continuum\Entity\CalendarEvent;
use Continuum\Enum\CalendarEventFormat;
use Continuum\Enum\CalendarEventType;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CombinedCalendarEvent::class)]
final class CombinedCalendarEventTest extends TestCase
{
    public function testConstructor(): void
    {
        $dayEvent = new CalendarEvent(
            datetime: new DateTimeImmutable(),
            format: CalendarEventFormat::Day,
            type: CalendarEventType::Blue,
            title: 'title',
        );
        $hourEvent = new CalendarEvent(
            datetime: new DateTimeImmutable(),
            format: CalendarEventFormat::Hour,
            type: CalendarEventType::Orange,
            title: 'title',
        );

        $dto = new CombinedCalendarEvent(
            dayEvent: $dayEvent,
            hourEvents: [$hourEvent],
        );

        self::assertSame($dayEvent, $dto->dayEvent);
        self::assertSame([$hourEvent], $dto->hourEvents);
    }

    public function testConstructorEmpty(): void
    {
        $dto = new CombinedCalendarEvent(
            dayEvent: null,
            hourEvents: [],
        );

        self::assertNull($dto->dayEvent);
        self::assertSame([], $dto->hourEvents);
    }
}
