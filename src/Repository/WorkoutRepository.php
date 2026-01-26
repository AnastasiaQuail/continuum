<?php

declare (strict_types=1);

namespace Continuum\Repository;

use Continuum\Entity\Workout;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

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
        return $this->createQueryBuilder('w')
            ->andWhere('w.id = :id')
            ->setParameter('id', $id)
            ->leftJoin('w.workoutExercises', 'we')
            ->addSelect('we')
            ->addOrderBy('we.orderIndex', 'ASC')
            ->leftJoin('we.exercise', 'e')
            ->addSelect('e')
            ->leftJoin('we.sets', 'ws')
            ->addSelect('ws')
            ->addOrderBy('ws.orderIndex', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return list<Workout>
     */
    public function findByMonth(DateTimeImmutable $month, DateTimeZone $timeZone): array
    {
        $from = new DateTimeImmutable(
            sprintf('%d-%d-01 00:00:00', $month->format('Y'), $month->format('m')),
            $timeZone
        );
        $to = new DateTimeImmutable(
            sprintf('%d-%d-%d 23:59:59', $month->format('Y'), $month->format('m'), $month->format('t')),
            $timeZone
        );

        return $this->createQueryBuilder('w')
            ->andWhere('w.date BETWEEN :from AND :to')
            ->setParameter('from', $from->setTimezone(new DateTimeZone('UTC')))
            ->setParameter('to', $to->setTimezone(new DateTimeZone('UTC')))
            ->addOrderBy('w.date', 'ASC')
            ->leftJoin('w.workoutExercises', 'we')
            ->addSelect('we')
            ->addOrderBy('we.orderIndex', 'ASC')
            ->leftJoin('we.exercise', 'e')
            ->addSelect('e')
            ->leftJoin('we.sets', 'ws')
            ->addSelect('ws')
            ->addOrderBy('ws.orderIndex', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function create(Workout $workout): void
    {
        $this->getEntityManager()->persist($workout);
        $this->getEntityManager()->flush();
    }
}
