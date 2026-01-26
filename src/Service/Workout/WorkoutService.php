<?php

declare(strict_types=1);

namespace Continuum\Service\Workout;

use Continuum\Entity\User;
use Continuum\Entity\Workout;
use Continuum\Repository\WorkoutRepository;
use DateTimeImmutable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

final readonly class WorkoutService
{
    public function __construct(
        private WorkoutRepository $repository,
    ) {}

    public function create(): Workout
    {
        $workout = new Workout();

        $this->repository->create($workout);

        return $workout;
    }

    public function getById(Uuid $id): Workout
    {
        return $this->repository->findOneById($id) ?? throw new NotFoundHttpException('Workout not found');
    }

    /**
     * @return list<Workout>
     */
    public function getByMonth(User $user, DateTimeImmutable $date): array
    {
        return $this->repository->findByMonth($date, $user->getTimezone());
    }

}
