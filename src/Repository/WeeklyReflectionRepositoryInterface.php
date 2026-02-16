<?php

declare(strict_types=1);

namespace Continuum\Repository;

use Continuum\Entity\WeeklyReflection;
use DateTimeImmutable;

interface WeeklyReflectionRepositoryInterface
{
    /**
     * @return list<WeeklyReflection>
     */
    public function findByDays(DateTimeImmutable ...$days): array;

    public function findOneByDay(DateTimeImmutable $day): ?WeeklyReflection;

    public function save(WeeklyReflection $weeklyReflection): void;
}
