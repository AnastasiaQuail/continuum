<?php

declare(strict_types=1);

namespace Continuum\Twig;

use DateTimeImmutable;
use Twig\Attribute\AsTwigFilter;

final readonly class DayIsWeekendFilter
{
    #[AsTwigFilter('is_weekend')]
    public function __invoke(string $day): bool
    {
        $date = new DateTimeImmutable($day);
        $dayOfWeek = (int) $date->format('N');

        return 6 === $dayOfWeek || 7 === $dayOfWeek;
    }
}
