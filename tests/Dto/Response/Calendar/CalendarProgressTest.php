<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Response\Calendar;

use Continuum\Dto\Response\Calendar\CalendarProgress;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(CalendarProgress::class)]
final class CalendarProgressTest extends TestCase
{
    #[DataProvider('provideGetCurrentProgressCases')]
    public function testGetCurrentProgress(DateInterval $past, DateInterval $total, float $expected): void
    {
        $dto = new CalendarProgress($past, $total);

        self::assertSame($expected, $dto->getCurrentProgress());
    }

    /**
     * @return iterable<array{0: DateInterval, 1: DateInterval, 2: float}>
     */
    public static function provideGetCurrentProgressCases(): iterable
    {
        $date = new DateTimeImmutable('2020-01-01');

        yield [
            $date->diff($date),
            $date->diff($date->modify('+10 days')),
            0.0,
        ];

        yield [
            $date->diff($date->modify('+4 days')),
            $date->diff($date->modify('+10 days')),
            40.0,
        ];

        yield [
            $date->diff($date->modify('+7 days')),
            $date->diff($date->modify('+9 days')),
            77.7,
        ];

        yield [
            $date->diff($date->modify('+10 days')),
            $date->diff($date->modify('+10 days')),
            100.0,
        ];

        yield [
            $date->diff($date->modify('+12 days')),
            $date->diff($date->modify('+10 days')),
            120.0,
        ];
    }

    #[DataProvider('provideGetWeeksCases')]
    public function testGetWeeks(
        DateInterval $past,
        int $pastDays,
        int $pastWeeks,
        DateInterval $total,
        int $totalWeeks
    ): void {
        $dto = new CalendarProgress($past, $total);

        self::assertSame($pastDays, $dto->getPastDays());
        self::assertSame($pastWeeks, $dto->getPastWeeks());
        self::assertSame($totalWeeks, $dto->getTotalWeeks());
    }

    /**
     * @return iterable<array{0: DateInterval, 1: int, 2: int, 3: DateInterval, 4: int}>
     */
    public static function provideGetWeeksCases(): iterable
    {
        $date = new DateTimeImmutable('2020-01-01');

        yield [
            $date->diff($date),
            0,
            0,
            $date->diff($date->modify('+5 days')),
            1,
        ];

        yield [
            $date->diff($date->modify('+3 days')),
            3,
            1,
            $date->diff($date->modify('+14 days')),
            2,
        ];

        yield [
            $date->diff($date->modify('+13 days')),
            13,
            2,
            $date->diff($date->modify('+15 days')),
            3,
        ];

        yield [
            $date->diff($date->modify('+15 days')),
            15,
            3,
            $date->diff($date->modify('+30 days')),
            5,
        ];

        yield [
            new DateInterval('P15D'),
            0,
            0,
            new DateInterval('P30D'),
            0,
        ];
    }

    #[DataProvider('provideGetMonthsCases')]
    public function testGetMonths(string $duration, int $months): void
    {
        $dto = new CalendarProgress(new DateInterval($duration), new DateInterval($duration));

        self::assertSame($months, $dto->getPastMonths());
        self::assertSame($months, $dto->getTotalMonths());
    }

    /**
     * @return iterable<array{0: non-empty-string, 1: non-negative-int}>
     */
    public static function provideGetMonthsCases(): iterable
    {
        yield ['P0Y0M0D', 0];

        yield ['P0Y0M1D', 1];

        yield ['P0Y2M0D', 2];

        yield ['P1Y1M1D', 14];
    }
}
