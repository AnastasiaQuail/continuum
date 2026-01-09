<?php

declare(strict_types=1);

namespace Continuum\Enum;

enum CalendarEventFormat: string
{
    case Hour = 'hour';
    case Day = 'day';
}
