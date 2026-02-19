<?php

declare(strict_types=1);

namespace Continuum\Dto\Response\Calendar;

use Continuum\Enum\CalendarEventType;

final readonly class ReportCalendarEvent
{
    /**
     * @param non-empty-string $title
     * @param positive-int $count
     */
    public function __construct(
        public string $title,
        public CalendarEventType $type,
        public int $count,
    ) {}
}
