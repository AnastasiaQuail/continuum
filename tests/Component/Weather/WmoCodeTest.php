<?php

declare(strict_types=1);

namespace Continuum\Tests\Component\Weather;

use Continuum\Component\Weather\WmoCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(WmoCode::class)]
final class WmoCodeTest extends TestCase
{
    /**
     * @param non-empty-string $expectedEmoji
     */
    #[DataProvider('provideWmoCodeEmojiCases')]
    public function testWmoCodeEmoji(WmoCode $code, string $expectedEmoji): void
    {
        self::assertSame($expectedEmoji, $code->toEmoji());
    }

    /**
     * @return iterable<array{WmoCode, non-empty-string}>
     */
    public static function provideWmoCodeEmojiCases(): iterable
    {
        yield [WmoCode::Sunny, '☀️'];

        yield [WmoCode::MainlySunny, '🌤️'];

        yield [WmoCode::PartlyCloudy, '🌥️'];

        yield [WmoCode::Cloudy, '☁️'];

        yield [WmoCode::Foggy, '😶‍🌫️'];

        yield [WmoCode::RimeFoggy, '😶‍🌫️'];

        yield [WmoCode::LightDrizzle, '🌧️'];

        yield [WmoCode::Drizzle, '🌧️'];

        yield [WmoCode::HeavyDrizzle, '🌧️'];

        yield [WmoCode::LightFreezingDrizzle, '🌧️'];

        yield [WmoCode::FreezingDrizzle, '🌧️'];

        yield [WmoCode::LightRain, '🌦️'];

        yield [WmoCode::Rain, '🌦️'];

        yield [WmoCode::HeavyRain, '🌦️'];

        yield [WmoCode::LightFreezingRain, '🌦️'];

        yield [WmoCode::FreezingRain, '🌦️'];

        yield [WmoCode::LightSnow, '❄️'];

        yield [WmoCode::Snow, '❄️'];

        yield [WmoCode::HeavySnow, '❄️'];

        yield [WmoCode::SnowGrains, '❄️'];

        yield [WmoCode::LightShowers, '🌧️'];

        yield [WmoCode::Showers, '🌧️'];

        yield [WmoCode::HeavyShowers, '🌧️'];

        yield [WmoCode::LightSnowShowers, '❄️'];

        yield [WmoCode::SnowShowers, '❄️'];

        yield [WmoCode::Thunderstorm, '⛈️'];

        yield [WmoCode::LightThunderstormsWithHail, '⛈️'];

        yield [WmoCode::ThunderstormsWithHail, '⛈️'];
    }
}
