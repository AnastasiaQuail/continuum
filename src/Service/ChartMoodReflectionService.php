<?php

declare(strict_types=1);

namespace Continuum\Service;

use Continuum\Dto\Response\Reflection\ChartMoodReflection;
use Continuum\Entity\MoodReflection;
use Continuum\Enum\MoodType;
use DateTimeImmutable;

final readonly class ChartMoodReflectionService
{
    /**
     * @param array<string, null|MoodReflection> $moodReflections
     *
     * @return list<ChartMoodReflection>
     */
    public function getChartMoodReflections(DateTimeImmutable $date, array $moodReflections): array
    {
        /** @var null|ChartMoodReflection $prevChart */
        $prevChart = null;
        $charts = [];

        foreach ($moodReflections as $moodReflection) {
            if (null === $moodReflection) {
                continue;
            }

            $charts[] = $prevChart = new ChartMoodReflection(
                type: match ($moodReflection->type) {
                    MoodType::Terrible => 1,
                    MoodType::Bad => 2,
                    MoodType::Okay => 3,
                    MoodType::Good => 4,
                    MoodType::Great => 5,
                },
                prevTime: $prevChart?->time,
                time: $moodReflection->date->getTimestamp() - $date->getTimestamp(),
                color: $moodReflection->type->getColor(),
            );
        }

        return $charts;
    }
}
