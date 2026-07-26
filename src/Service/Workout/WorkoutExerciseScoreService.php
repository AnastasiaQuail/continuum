<?php

declare(strict_types=1);

namespace Continuum\Service\Workout;

use Continuum\Entity\WorkoutSet;

final readonly class WorkoutExerciseScoreService
{
    public function getScore(WorkoutSet ...$sets): float
    {
        if ([] === $sets) {
            return 0;
        }

        $result = 0;
        $multiplier = 1;
        $count = 0;

        foreach ($sets as $set) {
            if ($set->isWarmup) {
                continue;
            }

            $weight = $set->weight;
            if (0.0 === $weight) {
                $weight = 10;
            }

            $result += ($set->reps * $weight) * $multiplier;
            $multiplier *= 1.05;
            ++$count;
        }

        if (0 === $count) {
            return 0;
        }

        return round($result / $count, 6);
    }
}
