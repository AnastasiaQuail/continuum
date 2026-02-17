<?php

declare(strict_types=1);

namespace Continuum\Repository;

use Continuum\Entity\Workout;
use Continuum\Entity\WorkoutExercise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Order;
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
        $result = $this->createQueryBuilder('we')
            ->select('MAX(we.orderIndex)')
            ->andWhere('we.workout = :id')
            ->setParameter('id', $workout->getId())
            ->getQuery()
            ->getSingleScalarResult();

        return (int) ($result ?? 0);
    }

    /**
     * @return list<WorkoutExercise>
     */
    public function findPrevByWorkout(Workout $workout): array
    {
        $sql = <<<'SQL'
            SELECT id
            FROM (
                SELECT w.id as workout_id, LAG(we.id) OVER (PARTITION BY we.exercise_id ORDER BY w.date) AS id
                FROM workout_exercises we
                JOIN workouts w ON w.id = we.workout_id
            ) as prev
            WHERE prev.id IS NOT NULL AND prev.workout_id = :workout_id;
            SQL;

        $ids = $this->getEntityManager()->getConnection()
            ->executeQuery($sql, ['workout_id' => $workout->getId()])
            ->fetchFirstColumn();

        if ([] === $ids) {
            return [];
        }

        return $this->createQueryBuilder('we')
            ->andWhere('we.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->innerJoin('we.exercise', 'e')
            ->addSelect('e')
            ->leftJoin('we.sets', 'ws')
            ->addSelect('ws')
            ->addOrderBy('ws.orderIndex', Order::Ascending->value)
            ->getQuery()
            ->getResult();
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
