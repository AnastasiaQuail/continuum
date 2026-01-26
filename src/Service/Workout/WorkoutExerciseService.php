<?php

declare(strict_types=1);

namespace Continuum\Service\Workout;

use Continuum\Entity\Exercise;
use Continuum\Entity\Workout;
use Continuum\Entity\WorkoutExercise;
use Continuum\Repository\WorkoutExerciseRepository;

final readonly class WorkoutExerciseService
{
    public function __construct(
        private WorkoutExerciseRepository $repository,
    ) {}

    public function create(Workout $workout, Exercise $exercise): WorkoutExercise
    {
        $workoutExercise = new WorkoutExercise($workout, $exercise);
        $workoutExercise->setOrderIndex(
            $this->repository->findMaxOrderIndexByWorkout($workout) + 1,
        );

        $this->repository->create($workoutExercise);

        return $workoutExercise;
    }

    public function delete(WorkoutExercise $workoutExercise): void
    {
        $this->repository->delete($workoutExercise);
    }
}
