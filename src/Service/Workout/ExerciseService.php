<?php

declare(strict_types=1);

namespace Continuum\Service\Workout;

use Continuum\Dto\Request\Workout\EditExercise;
use Continuum\Entity\Exercise;
use Continuum\Repository\ExerciseRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

final readonly class ExerciseService
{
    public function __construct(
        private ExerciseRepository $repository,
    ) {}

    /**
     * @return list<Exercise>
     */
    public function getAll(): array
    {
        return $this->repository->findOrdered();
    }

    /**
     * @return array<string, int>
     */
    public function getWorkoutExerciseCountIndexedById(): array
    {
        return $this->repository->findWorkoutExerciseCountIndexedById();
    }

    public function getById(Uuid $id): Exercise
    {
        return $this->repository->findOneById($id) ?? throw new NotFoundHttpException('Exercise not found');
    }

    public function create(EditExercise $dto): Exercise
    {
        $exercise = new Exercise(
            name: $dto->name,
            group: $dto->group,
        );

        $this->repository->save($exercise);

        return $exercise;
    }

    public function update(Exercise $exercise, EditExercise $dto): Exercise
    {
        $exercise->name = $dto->name;
        $exercise->group = $dto->group;

        $this->repository->save($exercise);

        return $exercise;
    }
}
