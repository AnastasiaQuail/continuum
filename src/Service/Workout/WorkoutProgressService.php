<?php

declare(strict_types=1);

namespace Continuum\Service\Workout;

use Continuum\Dto\Response\Workout\ExerciseProgress;
use Continuum\Entity\Workout;
use Continuum\Entity\WorkoutExercise;
use Continuum\Enum\Change;

final readonly class WorkoutProgressService
{
    public function __construct(
        private WorkoutExerciseService $workoutExerciseService,
        private WorkoutExerciseScoreService $workoutExerciseScoreService,
    ) {}

    /**
     * @param list<Workout> $workouts
     *
     * @return array<non-empty-string, ExerciseProgress>
     */
    public function getProgresses(array $workouts): array
    {
        $progresses = [];

        foreach ($workouts as $workout) {
            $prevExercises = $this->workoutExerciseService->getPrevExerciseMap($workout);

            foreach ($workout->getWorkoutExercises() as $exercise) {
                $id = (string) $exercise->getExercise()->getId();

                if (isset($prevExercises[$id])) {
                    $progresses[(string) $exercise->getId()] = $this->getProgress($prevExercises[$id], $exercise);
                }
            }
        }

        return $progresses;
    }

    public function getProgress(WorkoutExercise $prevExercise, WorkoutExercise $exercise): ExerciseProgress
    {
        $prevScore = $this->workoutExerciseScoreService->getScore($prevExercise);
        $score = $this->workoutExerciseScoreService->getScore($exercise);

        return new ExerciseProgress(
            change: match (true) {
                $score > $prevScore => Change::Increased,
                $score < $prevScore => Change::Decreased,
                default => Change::Unchanged,
            },
            percent: round(($score - $prevScore) / $prevScore * 100, 1),
        );
    }
}
