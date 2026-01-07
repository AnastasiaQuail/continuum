<?php

declare(strict_types=1);

namespace Continuum\Dto\Calendar;

use Continuum\Entity\CalendarDay;

final readonly class CombinedCalendarDay
{
    public function __construct(
        public ?CalendarDay $type = null,
        /**
         * @var array<CalendarDay>
         */
        public array $events = [],
    ) {}
}
