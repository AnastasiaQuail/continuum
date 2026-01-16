<?php

declare(strict_types=1);

namespace Continuum\Enum;

enum MoodType: string
{
    case Terrible = 'terrible';
    case Bad = 'bad';
    case Okay = 'okay';
    case Good = 'good';
    case Great = 'great';

    public function getColor(): Color
    {
        return match ($this) {
            self::Terrible => Color::Red,
            self::Bad => Color::Yellow,
            self::Okay => Color::Green,
            self::Good => Color::Sky,
            self::Great => Color::Indigo,
        };
    }
}
