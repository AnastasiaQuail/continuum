<?php

declare(strict_types=1);

namespace Continuum\Dto\Response\Calendar;

use Continuum\Entity\CalendarEvent;

final readonly class CombinedCalendarEvent
{
    public function __construct(
        public ?CalendarEvent $dayEvent = null,
        /**
         * @var array<CalendarEvent>
         */
        public array $hourEvents = [],
    ) {}
}
