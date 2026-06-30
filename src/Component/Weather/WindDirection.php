<?php

declare(strict_types=1);

namespace Continuum\Component\Weather;

enum WindDirection: string
{
    case North = 'N';
    case NorthEast = 'NE';
    case East = 'E';
    case SouthEast = 'SE';
    case South = 'S';
    case SouthWest = 'SW';
    case West = 'W';
    case NorthWest = 'NW';

    public static function fromDegrees(int $degrees): self
    {
        /** @var int<0, 7> $index */
        $index = floor(($degrees + 22.5) / 45) % 8;

        return match ($index) {
            0 => self::North,
            1 => self::NorthEast,
            2 => self::East,
            3 => self::SouthEast,
            4 => self::South,
            5 => self::SouthWest,
            6 => self::West,
            7 => self::NorthWest,
        };
    }

    public function toEmoji(): string
    {
        return match ($this) {
            self::North => '↓',
            self::NorthEast => '↙',
            self::East => '←',
            self::SouthEast => '↖',
            self::South => '↑',
            self::SouthWest => '↗',
            self::West => '→',
            self::NorthWest => '↘',
        };
    }
}
