<?php

declare(strict_types=1);

namespace Continuum\Repository;

use Continuum\Entity\WorkoutExercise;
use Continuum\Entity\WorkoutSet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorkoutSet>
 */
final class WorkoutSetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkoutSet::class);
    }

    public function findMaxOrderIndexByWorkout(WorkoutExercise $workoutExercise): int
    {
        return $this->createQueryBuilder('ws')
            ->select('MAX(ws.orderIndex)')
            ->andWhere('ws.workoutExercise = :id')
            ->setParameter('id', $workoutExercise->getId())
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    public function create(WorkoutSet $workoutSet): void
    {
        $this->getEntityManager()->persist($workoutSet);
        $this->getEntityManager()->flush();
    }

    public function delete(WorkoutSet $workoutSet): void
    {
        $this->getEntityManager()->remove($workoutSet);
        $this->getEntityManager()->flush();
    }
}
