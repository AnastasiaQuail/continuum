<?php

declare(strict_types=1);

namespace Continuum\Component\Weather;

/**
 * @see https://gist.github.com/stellasphere/9490c195ed2b53c707087c8c2db4ec0c
 */
enum WmoCode: int
{
    case Sunny = 0;
    case MainlySunny = 1;
    case PartlyCloudy = 2;
    case Cloudy = 3;
    case Foggy = 45;
    case RimeFoggy = 48;
    case LightDrizzle = 51;
    case Drizzle = 53;
    case HeavyDrizzle = 55;
    case LightFreezingDrizzle = 56;
    case FreezingDrizzle = 57;
    case LightRain = 61;
    case Rain = 63;
    case HeavyRain = 65;
    case LightFreezingRain = 66;
    case FreezingRain = 67;
    case LightSnow = 71;
    case Snow = 73;
    case HeavySnow = 75;
    case SnowGrains = 77;
    case LightShowers = 80;
    case Showers = 81;
    case HeavyShowers = 82;
    case LightSnowShowers = 85;
    case SnowShowers = 86;
    case Thunderstorm = 95;
    case LightThunderstormsWithHail = 96;
    case ThunderstormsWithHail = 99;

    public function toEmoji(): string
    {
        return match ($this) {
            self::Sunny => '☀️',

            self::MainlySunny => '🌤️',

            self::PartlyCloudy => '🌥️',

            self::Cloudy => '☁️',

            self::Foggy,
            self::RimeFoggy => '😶‍🌫️',

            self::LightDrizzle,
            self::Drizzle,
            self::HeavyDrizzle,
            self::LightFreezingDrizzle,
            self::FreezingDrizzle,
            self::LightShowers,
            self::Showers,
            self::HeavyShowers => '🌧️',

            self::LightRain,
            self::Rain,
            self::HeavyRain,
            self::LightFreezingRain,
            self::FreezingRain => '🌦️',

            self::LightSnow,
            self::Snow,
            self::HeavySnow,
            self::SnowGrains,
            self::LightSnowShowers,
            self::SnowShowers => '❄️',

            self::Thunderstorm,
            self::LightThunderstormsWithHail,
            self::ThunderstormsWithHail => '⛈️',
        };
    }
}
