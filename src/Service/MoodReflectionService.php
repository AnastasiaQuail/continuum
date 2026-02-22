<?php

declare(strict_types=1);

namespace Continuum\Service;

use Continuum\Dto\Request\Reflection\EditMoodReflection;
use Continuum\Entity\MoodReflection;
use Continuum\Repository\MoodReflectionRepositoryInterface;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class MoodReflectionService
{
    public function __construct(
        #[Autowire(param: 'app.reflection.mood_trend.days')]
        private int $trendDuration,
        private MoodReflectionRepositoryInterface $repository,
    ) {}

    /**
     * @return array<non-falsy-string, null|MoodReflection>
     */
    public function getPreviousDays(?int $days = null): array
    {
        $days ??= $this->trendDuration;

        $moods = [];
        for ($day = $days - 1; $day >= 0; --$day) {
            $date = new DateTimeImmutable(sprintf('-%d days', $day))->format('Y-m-d');
            $moods[$date] = null;
        }

        foreach ($this->repository->findPreviousDays($days) as $mood) {
            $moods[$mood->date->format('Y-m-d')] = $mood;
        }

        return $moods;
    }

    /**
     * @return array<string, MoodReflection>
     */
    public function getByMonth(DateTimeImmutable $date): array
    {
        $moods = [];
        foreach ($this->repository->findByMonth($date) as $mood) {
            $moods[$mood->date->format('Y-m-d')] = $mood;
        }

        return $moods;
    }

    public function findMoodByDay(DateTimeImmutable $day): ?MoodReflection
    {
        return $this->repository->findOneByDay($day);
    }

    public function save(DateTimeImmutable $day, ?MoodReflection $mood, EditMoodReflection $dto): MoodReflection
    {
        $mood ??= new MoodReflection($day);
        $mood->type = $dto->type;
        $mood->text = $dto->text;

        $this->repository->save($mood);

        return $mood;
    }
}
