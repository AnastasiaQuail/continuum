<?php

declare(strict_types=1);

namespace Continuum\Service\Workout;

use Continuum\Dto\Request\Workout\NewWorkoutSet;
use Continuum\Entity\WorkoutExercise;
use Continuum\Entity\WorkoutSet;
use Continuum\Repository\WorkoutSetRepository;

final readonly class WorkoutSetService
{
    public function __construct(
        private WorkoutSetRepository $repository,
    ) {}

    public function create(WorkoutExercise $workoutExercise, NewWorkoutSet $dto): WorkoutSet
    {
        $workoutSet = new WorkoutSet(
            workoutExercise: $workoutExercise,
            weight: $dto->weight,
            reps: $dto->reps,
            isWarmup: $dto->isWarmup(),
            orderIndex: $this->repository->findMaxOrderIndexByWorkoutExerciseId($workoutExercise->id) + 1,
        );

        $this->repository->create($workoutSet);

        return $workoutSet;
    }

    public function delete(WorkoutSet $workoutSet): void
    {
        $this->repository->delete($workoutSet);
    }
}
