<?php

declare(strict_types=1);

namespace Continuum\Service\Calendar;

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

    public function getCurrentProgress(): float
    {
        $start = new DateTimeImmutable($this->startDate);
        $end = new DateTimeImmutable($this->endDate);
        $current = new DateTimeImmutable();

        $totalDays = $start->diff($end)->days;
        $pastDays = $start->diff($current)->days;

        return floor($pastDays / $totalDays * 1000) / 10;
    }
}
