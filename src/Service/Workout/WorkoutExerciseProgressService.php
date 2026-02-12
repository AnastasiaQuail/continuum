<?php

declare(strict_types=1);

namespace Continuum\Service\Workout;

use Continuum\Dto\Response\Workout\ExerciseProgress;
use Continuum\Entity\Workout;
use Continuum\Entity\WorkoutExercise;
use Continuum\Enum\Change;

final readonly class WorkoutExerciseProgressService
{
    public function __construct(
        private WorkoutExerciseService $workoutExerciseService,
    ) {}

    /**
     * @param list<Workout> $workouts
     *
     * @return array<non-empty-string, ExerciseProgress>
     */
    public function getProgress(array $workouts): array
    {
        $progresses = [];

        foreach ($workouts as $workout) {
            $prevWorkoutExercises = $this->workoutExerciseService->getPrevExerciseMap($workout);

            foreach ($workout->getWorkoutExercises() as $workoutExercise) {
                $exerciseId = (string) $workoutExercise->getExercise()->getId();

                if (!isset($prevWorkoutExercises[$exerciseId])) {
                    continue;
                }

                $result = $this->getResult($workoutExercise);
                $prevResult = $this->getResult($prevWorkoutExercises[$exerciseId]);

                $progresses[(string) $workoutExercise->getId()] = new ExerciseProgress(
                    change: match (true) {
                        $result > $prevResult => Change::Increased,
                        $result < $prevResult => Change::Decreased,
                        default => Change::Unchanged,
                    },
                    percent: round(($result - $prevResult) / $prevResult * 100, 1),
                );
            }
        }

        return $progresses;
    }

    private function getResult(WorkoutExercise $workoutExercise): float
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
                $weight = 1;
            }

            $result += $set->getReps() * $weight;
            ++$count;
        }

        return $result / $count;
    }
}
