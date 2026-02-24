<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Response\Calendar;

use Continuum\Dto\Response\Calendar\UpcomingEvent;
use Continuum\Enum\CalendarEventType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(UpcomingEvent::class)]
final class UpcomingEventTest extends TestCase
{
    public function testConstructor(): void
    {
        $dto = new UpcomingEvent(
            type: CalendarEventType::Blue,
            title: 't',
            text: 'x',
        );

        self::assertSame(CalendarEventType::Blue, $dto->type);
        self::assertSame('t', $dto->title);
        self::assertSame('x', $dto->text);
    }
}
