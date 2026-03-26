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
        /** @var array<non-empty-string, WorkoutExercise> $prevWorkoutExercises */
        $prevWorkoutExercises = [];

        /** @var array<non-empty-string, WorkoutExercise> $unhandledWorkoutExercises */
        $unhandledWorkoutExercises = [];

        $progresses = [];

        foreach ($workouts as $workout) {
            foreach ($workout->workoutExercises as $workoutExercise) {
                $exerciseId = (string) $workoutExercise->exercise->id;

                if (isset($prevWorkoutExercises[$exerciseId])) {
                    $progresses[(string) $workoutExercise->id] = $this->getProgress(
                        $prevWorkoutExercises[$exerciseId],
                        $workoutExercise
                    );
                } else {
                    $unhandledWorkoutExercises[$exerciseId] = $workoutExercise;
                }

                $prevWorkoutExercises[$exerciseId] = $workoutExercise;
            }
        }

        if ([] !== $unhandledWorkoutExercises) {
            $prevWorkoutExercises = $this->workoutExerciseService->getPrevExerciseByIds(...$unhandledWorkoutExercises);

            foreach ($prevWorkoutExercises as $prevWorkoutExercise) {
                $exerciseId = (string) $prevWorkoutExercise->exercise->id;

                if (isset($unhandledWorkoutExercises[$exerciseId])) {
                    $workoutExercise = $unhandledWorkoutExercises[$exerciseId];

                    $progresses[(string) $workoutExercise->id] = $this->getProgress(
                        $prevWorkoutExercise,
                        $workoutExercise,
                    );
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
