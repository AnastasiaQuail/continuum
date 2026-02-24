<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Request\Calendar;

use Continuum\Dto\Request\Calendar\NewCalendarEvent;
use Continuum\Enum\CalendarEventType;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NewCalendarEvent::class)]
final class NewCalendarEventTest extends TestCase
{
    public function testConstructor(): void
    {
        $dto = new NewCalendarEvent(
            title: 't',
            type: CalendarEventType::Blue,
            time: $time = new DateTimeImmutable('-5 minutes'),
        );

        self::assertSame('t', $dto->title);
        self::assertSame(CalendarEventType::Blue, $dto->type);
        self::assertSame($time, $dto->time);
    }

    public function testWithoutTime(): void
    {
        $dto = new NewCalendarEvent(
            title: 't',
            type: CalendarEventType::Blue,
        );

        self::assertNull($dto->time);
    }
}
