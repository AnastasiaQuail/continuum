<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Response;

use Continuum\Dto\Response\CoupleTogetherInformation;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(CoupleTogetherInformation::class)]
final class CoupleTogetherInformationTest extends TestCase
{
    #[DataProvider('provideToStringCases')]
    public function testToString(string $duration, string $string, bool $isStartDay): void
    {
        $interval = new DateInterval($duration);

        $dto = new CoupleTogetherInformation($interval);

        self::assertSame($string, (string) $dto);
        self::assertSame($isStartDay, $dto->isStartDay());
    }

    /**
     * @return iterable<array{0: non-empty-string, 1: string, 2: bool}>
     */
    public static function provideToStringCases(): iterable
    {
        yield ['P0D', '', true];

        yield ['P1Y', '1 year', true];

        yield ['P2Y', '2 years', true];

        yield ['P1M', '1 month', true];

        yield ['P2M', '2 months', true];

        yield ['P1D', '1 day', false];

        yield ['P5D', '5 days', false];

        yield ['P1Y1M', '1 year and 1 month', true];

        yield ['P1Y0M5D', '1 year and 5 days', false];

        yield ['P1M3D', '1 month and 3 days', false];

        yield ['P1Y2M3D', '1 year, 2 months and 3 days', false];
    }

    public function testInvertedToString(): void
    {
        $interval = new DateInterval('P1Y');
        $interval->invert = 1;

        $dto = new CoupleTogetherInformation($interval);

        self::assertSame('', (string) $dto);
    }

    #[DataProvider('provideGetDaysCases')]
    public function testGetDays(DateTimeImmutable $date1, DateTimeImmutable $date2, int $days, int $expectedDays): void
    {
        $interval = $date1->diff($date2);
        $dto = new CoupleTogetherInformation($interval);

        self::assertSame($expectedDays, $dto->getDays($days));
    }

    /**
     * @return iterable<array{0: DateTimeImmutable, 1: DateTimeImmutable, 2: non-negative-int, 3: non-negative-int}>
     */
    public static function provideGetDaysCases(): iterable
    {
        yield [new DateTimeImmutable('2020-01-01'), new DateTimeImmutable('2020-01-01'), 0, 0];

        yield [new DateTimeImmutable('2020-01-01'), new DateTimeImmutable('2020-01-02'), 2, 1];

        yield [new DateTimeImmutable('2020-01-01'), new DateTimeImmutable('2020-01-08'), 10, 7];

        yield [new DateTimeImmutable('2020-01-01'), new DateTimeImmutable('2020-01-08'), 5, 5];
    }

    public function testGetDaysWithoutDiff(): void
    {
        $interval = new DateInterval('P7D');
        $dto = new CoupleTogetherInformation($interval);

        // $interval->days === false if the object was not created by diff() method
        self::assertSame(0, $dto->getDays(10));
    }
}
