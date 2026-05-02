<?php

declare(strict_types=1);

namespace Continuum\Service\Workout;

use Continuum\Entity\Exercise;
use Continuum\Entity\User;
use Continuum\Entity\Workout;
use Continuum\Entity\WorkoutExercise;
use Continuum\Repository\WorkoutExerciseRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class WorkoutExerciseService
{
    public function __construct(
        private WorkoutExerciseRepository $repository,
    ) {}

    public function create(Workout $workout, Exercise $exercise): WorkoutExercise
    {
        if (!$exercise->isActive) {
            throw new BadRequestHttpException('Exercise is not active.');
        }

        $workoutExercise = new WorkoutExercise(
            workout: $workout,
            exercise: $exercise,
            orderIndex: $this->repository->findMaxOrderIndexByWorkoutId($workout->id) + 1,
        );

        $this->repository->create($workoutExercise);

        return $workoutExercise;
    }

    public function delete(WorkoutExercise $workoutExercise): void
    {
        if (!$workoutExercise->sets->isEmpty()) {
            throw new BadRequestHttpException('Workout exercise has sets');
        }

        $this->repository->delete($workoutExercise);
    }

    /**
     * @return array<non-empty-string, non-empty-list<WorkoutExercise>>
     */
    public function getPreviousDays(int $days, User $user): array
    {
        $workoutExercises = [];
        foreach ($this->repository->findPreviousDays($days, $user->timezone) as $workoutExercise) {
            $workoutExercises[(string) $workoutExercise->exercise->id][] = $workoutExercise;
        }

        return $workoutExercises;
    }

    /**
     * @return array<non-empty-string, WorkoutExercise>
     */
    public function getPrevExerciseMap(Workout $workout): array
    {
        $workoutExercises = [];
        foreach ($this->repository->findPrevByWorkoutId($workout->id) as $workoutExercise) {
            $workoutExercises[(string) $workoutExercise->exercise->id] = $workoutExercise;
        }

        return $workoutExercises;
    }

    /**
     * @return list<WorkoutExercise>
     */
    public function getPrevExerciseByIds(WorkoutExercise ...$workoutExercises): array
    {
        $ids = [];
        foreach ($workoutExercises as $workoutExercise) {
            $ids[] = $workoutExercise->id;
        }

        return $this->repository->findPrevByIds(...$ids);
    }
}
