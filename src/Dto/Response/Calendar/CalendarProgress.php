<?php

declare(strict_types=1);

namespace Continuum\Dto\Response\Calendar;

use DateInterval;

final readonly class CalendarProgress
{
    public function __construct(
        private DateInterval $past,
        private DateInterval $total,
    ) {}

    public function getCurrentProgress(): float
    {
        return floor($this->past->days / $this->total->days * 1000) / 10;
    }

    public function getPastWeeks(): int
    {
        return (int) ceil($this->past->days / 7);
    }

    public function getTotalWeeks(): int
    {
        return (int) ceil($this->total->days / 7);
    }

    public function getPastMonths(): int
    {
        return $this->past->y * 12 + $this->past->m + ($this->past->d > 0 ? 1 : 0);
    }

    public function getTotalMonths(): int
    {
        return $this->total->y * 12 + $this->total->m + ($this->total->d > 0 ? 1 : 0);
    }
}
