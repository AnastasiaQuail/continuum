<?php

declare(strict_types=1);

namespace Continuum\Enum;

enum CalendarEventType: string
{
    case Red = Color::Red->value;
    case Orange = Color::Orange->value;
    case Amber = Color::Amber->value;
    case Yellow = Color::Yellow->value;
    case Lime = Color::Lime->value;
    case Emerald = Color::Emerald->value;
    case Teal = Color::Teal->value;
    case Cyan = Color::Cyan->value;
    case Sky = Color::Sky->value;
    case Blue = Color::Blue->value;
    case Indigo = Color::Indigo->value;
    case Violet = Color::Violet->value;
    case Purple = Color::Purple->value;
    case Fuchsia = Color::Fuchsia->value;
    case Pink = Color::Pink->value;
    case Rose = Color::Rose->value;
    case Slate = Color::Slate->value;
    case Stone = Color::Stone->value;

    public function getColor(): Color
    {
        return Color::from($this->value);
    }
}
