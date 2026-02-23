<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Response\Calendar;

use Continuum\Dto\Response\Calendar\ReportCalendarEvent;
use Continuum\Enum\CalendarEventType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ReportCalendarEvent::class)]
final class ReportCalendarEventTest extends TestCase
{
    public function testConstructor(): void
    {
        $dto = new ReportCalendarEvent(
            title: 't',
            type: CalendarEventType::Blue,
            count: 3,
        );

        self::assertSame('t', $dto->title);
        self::assertSame(CalendarEventType::Blue, $dto->type);
        self::assertSame(3, $dto->count);
    }
}
