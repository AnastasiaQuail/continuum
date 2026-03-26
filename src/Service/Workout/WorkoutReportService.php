<?php

declare(strict_types=1);

namespace Continuum\Service\Workout;

use Continuum\Dto\Response\Workout\WorkoutExerciseProgress;
use Continuum\Dto\Response\Workout\WorkoutReport;
use Continuum\Entity\Workout;
use Continuum\Entity\WorkoutExercise;

final readonly class WorkoutReportService
{
    public function __construct(
        private WorkoutProgressService $workoutProgressService,
    ) {}

    /**
     * @param list<Workout> $workouts
     */
    public function getReport(array $workouts): WorkoutReport
    {
        /** @var array<string, non-empty-list<WorkoutExercise>> $exercises */
        $exercises = [];

        foreach ($workouts as $workout) {
            foreach ($workout->workoutExercises as $workoutExercise) {
                $exerciseId = (string) $workoutExercise->exercise->id;

                $exercises[$exerciseId] ??= [];
                $exercises[$exerciseId][] = $workoutExercise;
            }
        }

        $progresses = [];

        foreach ($exercises as $workoutExercises) {
            if (count($workoutExercises) <= 1) {
                continue;
            }

            $prevWorkoutExercise = array_first($workoutExercises);
            $workoutExercise = array_last($workoutExercises);

            $progress = $this->workoutProgressService->getProgress($prevWorkoutExercise, $workoutExercise);

            if (!$progress->change->isUnchanged()) {
                $progresses[] = new WorkoutExerciseProgress(
                    exercise: $workoutExercise->exercise,
                    progress: $this->workoutProgressService->getProgress($prevWorkoutExercise, $workoutExercise),
                );
            }
        }

        usort(
            $progresses,
            static fn (
                WorkoutExerciseProgress $a,
                WorkoutExerciseProgress $b
            ): int => $b->progress->percent <=> $a->progress->percent,
        );

        return new WorkoutReport(
            count: count($workouts),
            duration: $this->getDuration($workouts),
            progresses: $progresses,
        );
    }

    /**
     * @param list<Workout> $workouts
     *
     * @return non-negative-int
     */
    private function getDuration(array $workouts): int
    {
        $duration = 0;

        foreach ($workouts as $workout) {
            $minTime = PHP_INT_MAX;
            $maxTime = 0;

            foreach ($workout->workoutExercises as $workoutExercise) {
                foreach ($workoutExercise->sets as $set) {
                    $setTime = $set->performedAt->getTimestamp();

                    if ($setTime < $minTime) {
                        $minTime = $setTime;
                    }

                    if ($setTime > $maxTime) {
                        $maxTime = $setTime;
                    }
                }
            }

            $offset = $maxTime - $minTime;

            if ($offset > 0) {
                $duration += $offset;
            }
        }

        return $duration;
    }
}
