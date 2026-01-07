<?php

declare(strict_types=1);

namespace Continuum\Dto\Calendar;

use Continuum\Enum\CalendarDayType;

final readonly class UpcomingNotification
{
    public function __construct(
        public CalendarDayType $type,
        public string $title,
        public string $text,
    ) {}
}
