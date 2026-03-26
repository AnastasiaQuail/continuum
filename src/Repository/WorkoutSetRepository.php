<?php

declare(strict_types=1);

namespace Continuum\Repository;

use Continuum\Entity\WorkoutSet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<WorkoutSet>
 */
final class WorkoutSetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkoutSet::class);
    }

    public function findMaxOrderIndexByWorkoutExerciseId(Uuid $id): int
    {
        $result = $this->createQueryBuilder('ws')
            ->select('MAX(ws.orderIndex)')
            ->andWhere('ws.workoutExercise = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) ($result ?? 0);
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
