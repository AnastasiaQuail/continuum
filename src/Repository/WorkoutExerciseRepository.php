<?php

declare(strict_types=1);

namespace Continuum\Repository;

use Continuum\Entity\Workout;
use Continuum\Entity\WorkoutExercise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorkoutExercise>
 */
final class WorkoutExerciseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkoutExercise::class);
    }

    public function findMaxOrderIndexByWorkout(Workout $workout): int
    {
        return $this->createQueryBuilder('we')
            ->select('MAX(we.orderIndex)')
            ->andWhere('we.workout = :id')
            ->setParameter('id', $workout->getId())
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    public function create(WorkoutExercise $workoutExercise): void
    {
        $this->getEntityManager()->persist($workoutExercise);
        $this->getEntityManager()->flush();
    }

    public function delete(WorkoutExercise $workoutExercise): void
    {
        $this->getEntityManager()->remove($workoutExercise);
        $this->getEntityManager()->flush();
    }
}
