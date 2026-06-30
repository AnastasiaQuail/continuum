<?php

declare(strict_types=1);

namespace Continuum\Tests\Component\Weather\Dto;

use Continuum\Component\Weather\Dto\Weather;
use Continuum\Component\Weather\Dto\Wind;
use Continuum\Component\Weather\WindDirection;
use Continuum\Component\Weather\WmoCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Weather::class)]
final class WeatherTest extends TestCase
{
    public function testConstructor(): void
    {
        $temperature = 15.5;
        $code = WmoCode::Sunny;
        $wind = new Wind(speed: 10.0, direction: WindDirection::North);

        $weather = new Weather(
            temperature: $temperature,
            code: $code,
            wind: $wind,
        );

        self::assertSame($temperature, $weather->temperature);
        self::assertSame($code, $weather->code);
        self::assertSame($wind, $weather->wind);
    }

    public function testConstructorWithNegativeTemperature(): void
    {
        $temperature = -18.2;
        $code = WmoCode::Snow;

        $weather = new Weather(
            temperature: $temperature,
            code: $code,
            wind: new Wind(speed: 0.0, direction: WindDirection::North),
        );

        self::assertSame($temperature, $weather->temperature);
        self::assertSame($code, $weather->code);
    }

    public function testConstructorWithNull(): void
    {
        $weather = new Weather(
            temperature: 10.0,
        );

        self::assertNull($weather->code);
        self::assertNull($weather->wind);
    }
}
