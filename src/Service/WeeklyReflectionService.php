<?php

declare(strict_types=1);

namespace Continuum\Service;

use Continuum\Dto\Request\Reflection\EditWeeklyReflection;
use Continuum\Entity\TextField;
use Continuum\Entity\WeeklyReflection;
use Continuum\Repository\WeeklyReflectionRepositoryInterface;
use DateTimeImmutable;

final readonly class WeeklyReflectionService
{
    public function __construct(
        private WeeklyReflectionRepositoryInterface $repository,
    ) {}

    /**
     * @return array<string, null|WeeklyReflection>
     */
    public function getByMonth(DateTimeImmutable $month, ?DateTimeImmutable $currentDate = null): array
    {
        $days = [];
        $reflections = [];

        $month = $month->modify('first day of this month');
        $sunday = $month->modify('sunday');
        $endDate = $month->modify('+1 month');

        if (null !== $currentDate) {
            $currentDate = $currentDate->modify('sunday');
        }

        do {
            $date = $sunday->format('Y-m-d');

            if (null === $currentDate || $date <= $currentDate->format('Y-m-d')) {
                $days[] = $sunday;
                $reflections[$sunday->format('Y-m-d')] = null;
            }

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
        $joy = new TextField($dto->joy, $dto->isJoyPrivate);
        $difficulty = new TextField($dto->difficulty, $dto->isDifficultyPrivate);
        $achievement = new TextField($dto->achievement, $dto->isAchievementPrivate);

        if (null === $weeklyReflection) {
            $weeklyReflection = new WeeklyReflection($week, $joy, $difficulty, $achievement);
        } else {
            $weeklyReflection->joy = $joy;
            $weeklyReflection->difficulty = $difficulty;
            $weeklyReflection->achievement = $achievement;
        }

        $this->repository->save($weeklyReflection);

        return $weeklyReflection;
    }
}
