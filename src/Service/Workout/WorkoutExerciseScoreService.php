<?php

declare(strict_types=1);

namespace Continuum\Service\Workout;

use Continuum\Entity\WorkoutExercise;

final readonly class WorkoutExerciseScoreService
{
    public function getScore(WorkoutExercise $workoutExercise): float
    {
        if ($workoutExercise->getSets()->isEmpty()) {
            return 0;
        }

        $result = 0;
        $count = 0;

        foreach ($workoutExercise->getSets() as $set) {
            if ($set->isWarmup()) {
                continue;
            }

            $weight = $set->getWeight();
            if (0.0 === $weight) {
                $weight = 10;
            }

            $result += $set->getReps() * $weight;
            ++$count;
        }

        if (0 === $count) {
            return 0;
        }

        return $result / $count;
    }
}
