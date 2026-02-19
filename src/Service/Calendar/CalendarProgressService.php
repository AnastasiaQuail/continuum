<?php

declare(strict_types=1);

namespace Continuum\Service\Calendar;

use Continuum\Dto\Response\Calendar\CalendarProgress;
use Continuum\Dto\Response\Calendar\CalendarReportProgress;
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

    public function getProgress(User $user, DateTimeImmutable $date): CalendarProgress
    {
        $start = new DateTimeImmutable($this->startDate, $user->timezone);
        $end = new DateTimeImmutable($this->endDate, $user->timezone);

        return new CalendarProgress(
            past: $start->diff($date),
            total: $start->diff($end),
        );
    }

    public function getReportProgress(
        User $user,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate
    ): CalendarReportProgress {
        $startProgress = $this->getProgress($user, $startDate);
        $endProgress = $this->getProgress($user, $endDate);

        return new CalendarReportProgress(
            startProgress: $startProgress,
            endProgress: $endProgress,
        );
    }
}
