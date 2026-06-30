<?php

declare(strict_types=1);

namespace Continuum\Tests\Component\Weather\Dto;

use Continuum\Component\Weather\Dto\Wind;
use Continuum\Component\Weather\WindDirection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Wind::class)]
final class WindTest extends TestCase
{
    public function testConstructor(): void
    {
        $speed = 15.5;
        $direction = WindDirection::South;

        $wind = new Wind(
            speed: $speed,
            direction: $direction,
        );

        self::assertSame($speed, $wind->speed);
        self::assertSame($direction, $wind->direction);
    }

    public function testConstructorWithZeroValues(): void
    {
        $speed = 0.0;
        $direction = WindDirection::NorthWest;

        $wind = new Wind(
            speed: $speed,
            direction: $direction,
        );

        self::assertSame($speed, $wind->speed);
        self::assertSame($direction, $wind->direction);
    }

    public function testConstructorWithNegativeValues(): void
    {
        $speed = -100.4;
        $direction = WindDirection::East;

        $wind = new Wind(
            speed: $speed,
            direction: $direction,
        );

        self::assertSame($speed, $wind->speed);
        self::assertSame($direction, $wind->direction);
    }
}
