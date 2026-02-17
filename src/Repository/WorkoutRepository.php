<?php

declare(strict_types=1);

namespace Continuum\Repository;

use Continuum\Entity\Workout;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

use function assert;

/**
 * @extends ServiceEntityRepository<Workout>
 */
final class WorkoutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Workout::class);
    }

    public function findOneById(Uuid $id): ?Workout
    {
        $result = $this->createQueryBuilder('w')
            ->andWhere('w.id = :id')
            ->setParameter('id', $id)
            ->leftJoin('w.workoutExercises', 'we')
            ->addSelect('we')
            ->addOrderBy('we.orderIndex', Order::Ascending->value)
            ->leftJoin('we.exercise', 'e')
            ->addSelect('e')
            ->leftJoin('we.sets', 'ws')
            ->addSelect('ws')
            ->addOrderBy('ws.orderIndex', Order::Ascending->value)
            ->getQuery()
            ->getOneOrNullResult();

        assert(null === $result || $result instanceof Workout);

        return $result;
    }

    /**
     * @return list<Workout>
     */
    public function findByRange(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.date BETWEEN :from AND :to')
            ->setParameter('from', $from->setTimezone(new DateTimeZone('UTC')))
            ->setParameter('to', $to->setTimezone(new DateTimeZone('UTC')))
            ->addOrderBy('w.date', Order::Ascending->value)
            ->leftJoin('w.workoutExercises', 'we')
            ->addSelect('we')
            ->addOrderBy('we.orderIndex', Order::Ascending->value)
            ->leftJoin('we.exercise', 'e')
            ->addSelect('e')
            ->leftJoin('we.sets', 'ws')
            ->addSelect('ws')
            ->addOrderBy('ws.orderIndex', Order::Ascending->value)
            ->getQuery()
            ->getResult();
    }

    public function create(Workout $workout): void
    {
        $this->getEntityManager()->persist($workout);
        $this->getEntityManager()->flush();
    }

    public function delete(Workout $workout): void
    {
        $this->getEntityManager()->remove($workout);
        $this->getEntityManager()->flush();
    }
}
