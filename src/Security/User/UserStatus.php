<?php

declare(strict_types=1);

namespace Continuum\Security\User;

use Continuum\Enum\Color;

enum UserStatus: int
{
    case Created = 0;
    case Active = 1;
    case Disabled = 2;

    public function getColor(): Color
    {
        return match ($this) {
            self::Created => Color::Slate,
            self::Active => Color::Green,
            self::Disabled => Color::Red,
        };
    }
}
