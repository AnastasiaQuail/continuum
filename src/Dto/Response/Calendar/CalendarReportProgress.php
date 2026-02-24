<?php

declare(strict_types=1);

namespace Continuum\Dto\Response\Calendar;

final readonly class CalendarReportProgress
{
    public function __construct(
        public CalendarProgress $startProgress,
        public CalendarProgress $endProgress,
    ) {}

    public function getOffset(): ?float
    {
        if ($this->startProgress->getPastDays() > $this->endProgress->getPastDays()) {
            return null;
        }

        return $this->endProgress->getCurrentProgress() - $this->startProgress->getCurrentProgress();
    }
}
