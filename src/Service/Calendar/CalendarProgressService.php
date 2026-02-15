<?php

declare(strict_types=1);

namespace Continuum\Service\Calendar;

use Continuum\Dto\Response\Calendar\CalendarProgress;
use Continuum\Entity\User;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class CalendarProgressService
{
    public function __construct(
        #[Autowire(env: 'string:CALENDAR_DATE_START')]
        private string $startDate,
        #[Autowire(env: 'string:CALENDAR_DATE_END')]
        private string $endDate,
    ) {}

    public function getProgress(User $user): CalendarProgress
    {
        $start = new DateTimeImmutable($this->startDate, $user->timezone);
        $end = new DateTimeImmutable($this->endDate, $user->timezone);
        $current = new DateTimeImmutable('now', $user->timezone);

        return new CalendarProgress(
            past: $start->diff($current),
            total: $start->diff($end),
        );
    }
}
