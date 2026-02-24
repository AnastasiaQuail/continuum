<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Response\Calendar;

use Continuum\Dto\Response\Calendar\CalendarProgress;
use Continuum\Dto\Response\Calendar\CalendarReportProgress;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(CalendarReportProgress::class)]
final class CalendarReportProgressTest extends TestCase
{
    #[DataProvider('provideGetOffsetCases')]
    public function testGetOffset(CalendarProgress $start, CalendarProgress $end, ?float $expected): void
    {
        $dto = new CalendarReportProgress(
            startProgress: $start,
            endProgress: $end,
        );

        self::assertSame($expected, $dto->getOffset());
    }

    /**
     * @return iterable<array{0: CalendarProgress, 1: CalendarProgress, 2: null|float}>
     */
    public static function provideGetOffsetCases(): iterable
    {
        $date = new DateTimeImmutable('2020-01-01');

        $startInterval = $date->diff($date);
        $totalInterval = $date->diff($date->modify('+100 days'));

        yield [
            new CalendarProgress(
                past: $startInterval,
                total: $totalInterval,
            ),
            new CalendarProgress(
                past: $startInterval,
                total: $totalInterval,
            ),
            0.0,
        ];

        yield [
            new CalendarProgress(
                past: $startInterval,
                total: $totalInterval,
            ),
            new CalendarProgress(
                past: $date->diff($date->modify('+5 days')),
                total: $totalInterval,
            ),
            5.0,
        ];

        yield [
            new CalendarProgress(
                past: $date->diff($date->modify('+47 days')),
                total: $totalInterval,
            ),
            new CalendarProgress(
                past: $date->diff($date->modify('+82 days')),
                total: $totalInterval,
            ),
            35.0,
        ];

        yield [
            new CalendarProgress(
                past: $date->diff($date->modify('+3 days')),
                total: $totalInterval,
            ),
            new CalendarProgress(
                past: $date->diff($date->modify('+91 days')),
                total: $totalInterval,
            ),
            88.0,
        ];

        yield [
            new CalendarProgress(
                past: $date->diff($date->modify('+10 days')),
                total: $totalInterval,
            ),
            new CalendarProgress(
                past: $date->diff($date->modify('+9 days')),
                total: $totalInterval,
            ),
            null,
        ];
    }
}
