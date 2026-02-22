<?php

declare(strict_types=1);

namespace Continuum\Tests\Entity;

use Continuum\Entity\Location;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Location::class)]
final class LocationTest extends TestCase
{
    public function testCreate(): void
    {
        $location = new Location(
            latitude: 123.456789,
            longitude: -98.765432,
        );

        self::assertSame(123.456789, $location->latitude);
        self::assertSame(-98.765432, $location->longitude);
    }

    public function testRounded(): void
    {
        $location = new Location(
            latitude: 123.456789191,
            longitude: -98.765432919,
        );

        self::assertSame(123.456789, $location->latitude);
        self::assertSame(-98.765433, $location->longitude);
    }

    public function testGetDistanceZero(): void
    {
        $location = new Location(latitude: 10.0, longitude: 20.0);

        $distance = $location->getDistance($location);

        self::assertSame(0.0, $distance);
    }

    #[DataProvider('provideGetDistanceCases')]
    public function testGetDistance(float $la1, float $lo1, float $la2, float $lo2, float $expected): void
    {
        $location = new Location(latitude: $la1, longitude: $lo1);
        $otherLocation = new Location(latitude: $la2, longitude: $lo2);

        $distance = $location->getDistance($otherLocation);

        self::assertSame($expected, round($distance, 3));
    }

    /**
     * @return iterable<array{0: float, 1: float, 2: float, 3: float, 4: float}>
     */
    public static function provideGetDistanceCases(): iterable
    {
        yield [52.234234, -28.718373, 100.929832, 1.032384, 5283.704];

        yield [12.000821, 124.111829, -86.193471, 34.932419, 11332.93];

        yield [-29.239921, 24.923939, 54.294387, 12.398472, 9366.253];
    }
}
