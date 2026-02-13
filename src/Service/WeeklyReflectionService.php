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
            $reflections[$reflection->getDate()->format('Y-m-d')] = $reflection;
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
        $weeklyReflection ??= new WeeklyReflection($week);
        $weeklyReflection->setJoy($dto->joy);
        $weeklyReflection->setIsJoyPrivate($dto->isJoyPrivate);
        $weeklyReflection->setDifficulty($dto->difficulty);
        $weeklyReflection->setIsDifficultyPrivate($dto->isDifficultyPrivate);
        $weeklyReflection->setAchievement($dto->achievement);
        $weeklyReflection->setIsAchievementPrivate($dto->isAchievementPrivate);

        $this->repository->save($weeklyReflection);

        return $weeklyReflection;
    }
}
