<?php

declare(strict_types=1);

namespace Continuum\Service\Workout;

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

    public function getById(Uuid $id): Exercise
    {
        return $this->repository->findOneById($id) ?? throw new NotFoundHttpException('Exercise not found');
    }
}
