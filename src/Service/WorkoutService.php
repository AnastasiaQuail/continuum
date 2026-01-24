<?php

declare(strict_types=1);

namespace Continuum\Service;

use Continuum\Entity\User;
use Continuum\Entity\Workout;
use Continuum\Repository\WorkoutRepository;
use DateTimeImmutable;

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

    /**
     * @return list<Workout>
     */
    public function getByMonth(User $user, DateTimeImmutable $date): array
    {
        return $this->repository->findByMonth($date, $user->getTimezone());
    }

}
