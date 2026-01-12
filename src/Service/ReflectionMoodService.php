<?php

declare(strict_types=1);

namespace Continuum\Service;

use Continuum\Entity\ReflectionMood;
use Continuum\Repository\ReflectionMoodRepository;
use DateTimeImmutable;

final readonly class ReflectionMoodService
{
    private const int PREVIOUS_DAYS = 60;

    public function __construct(
        private ReflectionMoodRepository $reflectionMoodRepository,
    ) {}

    /**
     * @return non-empty-array<string, ReflectionMood>
     */
    public function getPreviousDays(): array
    {
        $moods = [];
        for ($day = self::PREVIOUS_DAYS - 1; $day >= 0; $day--) {
            $moods[new DateTimeImmutable(sprintf('-%d days', $day))->format('Y-m-d')] = null;
        }

        foreach ($this->reflectionMoodRepository->findPreviousDays(self::PREVIOUS_DAYS) as $mood) {
            $moods[$mood->getDate()->format('Y-m-d')] = $mood;
        }

        return $moods;
    }

    /**
     * @return array<string, ReflectionMood>
     */
    public function getByMonth(DateTimeImmutable $date): array
    {
        $year = (int) $date->format('Y');
        $month = (int) $date->format('n');

        $moods = [];
        foreach ($this->reflectionMoodRepository->findByMonth($year, $month) as $mood) {
            $moods[$mood->getDate()->format('Y-m-d')] = $mood;
        }

        return $moods;
    }
}
