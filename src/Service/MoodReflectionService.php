<?php

declare(strict_types=1);

namespace Continuum\Service;

use Continuum\Dto\Request\Reflection\EditMoodReflection;
use Continuum\Entity\MoodReflection;
use Continuum\Repository\MoodReflectionRepository;
use DateTimeImmutable;

final readonly class MoodReflectionService
{
    private const int PREVIOUS_DAYS = 60;

    public function __construct(
        private MoodReflectionRepository $moodReflectionRepository,
    ) {}

    /**
     * @return non-empty-array<string, MoodReflection>
     */
    public function getPreviousDays(): array
    {
        $moods = [];
        for ($day = self::PREVIOUS_DAYS - 1; $day >= 0; $day--) {
            $moods[new DateTimeImmutable(sprintf('-%d days', $day))->format('Y-m-d')] = null;
        }

        foreach ($this->moodReflectionRepository->findPreviousDays(self::PREVIOUS_DAYS) as $mood) {
            $moods[$mood->getDate()->format('Y-m-d')] = $mood;
        }

        return $moods;
    }

    /**
     * @return array<string, MoodReflection>
     */
    public function getByMonth(DateTimeImmutable $date): array
    {
        $year = (int) $date->format('Y');
        $month = (int) $date->format('n');

        $moods = [];
        foreach ($this->moodReflectionRepository->findByMonth($year, $month) as $mood) {
            $moods[$mood->getDate()->format('Y-m-d')] = $mood;
        }

        return $moods;
    }

    public function findMoodByDay(DateTimeImmutable $day): ?MoodReflection
    {
        return $this->moodReflectionRepository->findOneByDay($day);
    }

    public function save(DateTimeImmutable $day, ?MoodReflection $mood, EditMoodReflection $dto): MoodReflection
    {
        $mood ??= new MoodReflection($day);
        $mood->setType($dto->type);
        $mood->setText($dto->text);

        $this->moodReflectionRepository->save($mood);

        return $mood;
    }
}
