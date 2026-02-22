<?php

declare(strict_types=1);

namespace Continuum\Repository;

use Continuum\Entity\MoodReflection;
use DateTimeImmutable;

interface MoodReflectionRepositoryInterface
{
    /**
     * @return list<MoodReflection>
     */
    public function findPreviousDays(int $days): array;

    /**
     * @return list<MoodReflection>
     */
    public function findByMonth(DateTimeImmutable $month): array;

    public function findOneByDay(DateTimeImmutable $day): ?MoodReflection;

    public function save(MoodReflection $moodReflection): void;
}
