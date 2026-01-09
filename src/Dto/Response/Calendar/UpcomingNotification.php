<?php

declare(strict_types=1);

namespace Continuum\Dto\Response\Calendar;

use Continuum\Enum\CalendarEventType;

final readonly class UpcomingNotification
{
    public function __construct(
        public CalendarEventType $type,
        public string $title,
        public string $text,
    ) {}
}
