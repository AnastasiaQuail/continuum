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

    public function toCalendarEventType(): CalendarEventType
    {
        return match ($this) {
            self::Terrible => CalendarEventType::Red,
            self::Bad => CalendarEventType::Yellow,
            self::Okay => CalendarEventType::Teal,
            self::Good => CalendarEventType::Cyan,
            self::Great => CalendarEventType::Blue,
        };
    }
}
