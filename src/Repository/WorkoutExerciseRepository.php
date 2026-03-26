<?php

declare(strict_types=1);

namespace Continuum\Repository;

use Continuum\Entity\WorkoutExercise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<WorkoutExercise>
 */
final class WorkoutExerciseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkoutExercise::class);
    }

    public function findMaxOrderIndexByWorkoutId(Uuid $workoutId): int
    {
        $result = $this->createQueryBuilder('we')
            ->select('MAX(we.orderIndex)')
            ->andWhere('we.workout = :id')
            ->setParameter('id', $workoutId)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) ($result ?? 0);
    }

    /**
     * @return list<WorkoutExercise>
     */
    public function findPrevByWorkoutId(Uuid $workoutId): array
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

        $prevIds = $this->getEntityManager()->getConnection()
            ->executeQuery($sql, ['workout_id' => $workoutId])
            ->fetchFirstColumn();

        if ([] === $prevIds) {
            return [];
        }

        return $this->createQueryBuilder('we')
            ->andWhere('we.id IN (:ids)')
            ->setParameter('ids', $prevIds)
            ->innerJoin('we.exercise', 'e')
            ->addSelect('e')
            ->leftJoin('we.sets', 'ws')
            ->addSelect('ws')
            ->addOrderBy('ws.orderIndex', Order::Ascending->value)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<WorkoutExercise>
     */
    public function findPrevByIds(Uuid ...$ids): array
    {
        if ([] === $ids) {
            return [];
        }

        $sql = <<<'SQL'
            SELECT id
            FROM (
                SELECT we.id as next_id, LAG(we.id) OVER (PARTITION BY we.exercise_id ORDER BY w.date) AS id
                FROM workout_exercises we
                JOIN workouts w ON w.id = we.workout_id
            ) as prev
            WHERE prev.id IS NOT NULL AND prev.next_id IN (:ids);
            SQL;

        $prevIds = $this->getEntityManager()->getConnection()
            ->executeQuery($sql, ['ids' => $ids], ['ids' => ArrayParameterType::STRING])
            ->fetchFirstColumn();

        if ([] === $prevIds) {
            return [];
        }

        return $this->createQueryBuilder('we')
            ->andWhere('we.id IN (:ids)')
            ->setParameter('ids', $prevIds)
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
