<?php

declare(strict_types=1);

namespace Continuum\Service\Workout;

use Continuum\Entity\WorkoutExercise;

final readonly class WorkoutExerciseScoreService
{
    public function getScore(WorkoutExercise $workoutExercise): float
    {
        if ($workoutExercise->sets->isEmpty()) {
            return 0;
        }

        $result = 0;
        $count = 0;

        foreach ($workoutExercise->sets as $set) {
            if ($set->isWarmup) {
                continue;
            }

            $weight = $set->weight;
            if (0.0 === $weight) {
                $weight = 10;
            }

            $result += $set->reps * $weight;
            ++$count;
        }

        if (0 === $count) {
            return 0;
        }

        return $result / $count;
    }
}
