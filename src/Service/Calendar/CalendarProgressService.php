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
        #[Autowire(env: 'string:APP_DATE_START')]
        private string $startDate,
        #[Autowire(env: 'string:APP_DATE_END')]
        private string $endDate,
    ) {}

    public function getProgress(User $user): CalendarProgress
    {
        $start = new DateTimeImmutable($this->startDate, $user->getTimezone());
        $end = new DateTimeImmutable($this->endDate, $user->getTimezone());
        $current = new DateTimeImmutable('now', $user->getTimezone());

        return new CalendarProgress(
            past: $start->diff($current),
            total: $start->diff($end),
        );
    }
}
