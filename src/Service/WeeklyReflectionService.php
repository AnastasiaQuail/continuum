<?php

declare(strict_types=1);

namespace Continuum\Service;

use Continuum\Dto\Request\Reflection\EditWeeklyReflection;
use Continuum\Entity\WeeklyReflection;
use Continuum\Repository\WeeklyReflectionRepository;
use DateTimeImmutable;

final readonly class WeeklyReflectionService
{
    public function __construct(
        private WeeklyReflectionRepository $repository,
    ) {}

    /**
     * @return array<string, null|WeeklyReflection>
     */
    public function getByMonth(DateTimeImmutable $month): array
    {
        $days = [];
        $reflections = [];

        $sunday = $month->modify('sunday');
        $endDate = $month->modify('+1 month');

        do {
            $days[] = $sunday;
            $reflections[$sunday->format('Y-m-d')] = null;

            $sunday = $sunday->modify('+1 week');
        } while ($sunday < $endDate);

        foreach ($this->repository->findByDays(...$days) as $reflection) {
            $reflections[$reflection->date->format('Y-m-d')] = $reflection;
        }

        return $reflections;
    }

    public function findByWeek(DateTimeImmutable $week): ?WeeklyReflection
    {
        return $this->repository->findOneByDay($week);
    }

    public function save(
        DateTimeImmutable $week,
        ?WeeklyReflection $weeklyReflection,
        EditWeeklyReflection $dto
    ): WeeklyReflection {
        if (null === $weeklyReflection) {
            $weeklyReflection = new WeeklyReflection(
                date: $week,
                joy: $dto->joy,
                difficulty: $dto->difficulty,
                achievement: $dto->achievement,
            );
        } else {
            $weeklyReflection->joy = $dto->joy;
            $weeklyReflection->difficulty = $dto->difficulty;
            $weeklyReflection->achievement = $dto->achievement;
        }

        $weeklyReflection->isJoyPrivate = $dto->isJoyPrivate;
        $weeklyReflection->isDifficultyPrivate = $dto->isDifficultyPrivate;
        $weeklyReflection->isAchievementPrivate = $dto->isAchievementPrivate;

        $this->repository->save($weeklyReflection);

        return $weeklyReflection;
    }
}
