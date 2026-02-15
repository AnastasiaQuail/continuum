<?php

declare(strict_types=1);

namespace Continuum\Service\Workout;

use Continuum\Entity\User;
use Continuum\Entity\Workout;
use Continuum\Repository\WorkoutRepository;
use DateTimeImmutable;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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
    public function getByMonth(User $user, DateTimeImmutable $month): array
    {
        $from = new DateTimeImmutable(
            sprintf('%s-%s-01 00:00:00', $month->format('Y'), $month->format('m')),
            $user->timezone
        );
        $to = new DateTimeImmutable(
            sprintf('%s-%s-%s 23:59:59', $month->format('Y'), $month->format('m'), $month->format('t')),
            $user->timezone
        );

        return $this->repository->findByRange($from, $to);
    }

    /**
     * @return list<Workout>
     */
    public function getByRange(User $user, DateTimeImmutable $fromDay, DateTimeImmutable $toDay): array
    {
        $from = new DateTimeImmutable(
            sprintf('%s-%s-%s 00:00:00', $fromDay->format('Y'), $fromDay->format('m'), $fromDay->format('d')),
            $user->timezone
        );
        $to = new DateTimeImmutable(
            sprintf('%s-%s-%s 23:59:59', $toDay->format('Y'), $toDay->format('m'), $toDay->format('d')),
            $user->timezone
        );

        return $this->repository->findByRange($from, $to);
    }

    public function delete(Workout $workout): void
    {
        if (!$workout->getWorkoutExercises()->isEmpty()) {
            throw new BadRequestHttpException('Workout has exercises');
        }

        $this->repository->delete($workout);
    }
}
