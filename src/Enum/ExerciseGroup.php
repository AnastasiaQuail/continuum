<?php

declare(strict_types=1);

namespace Continuum\Enum;

enum ExerciseGroup: string
{
    case Back = 'back';
    case Shoulders = 'shoulders';
    case Arms = 'arms';
    case Chest = 'chest';
    case Core = 'core';
    case Legs = 'legs';

    public function getColor(): Color
    {
        return match ($this) {
            self::Back => Color::Orange,
            self::Shoulders => Color::Yellow,
            self::Arms => Color::Green,
            self::Chest => Color::Teal,
            self::Core => Color::Sky,
            self::Legs => Color::Indigo,
        };
    }
}
