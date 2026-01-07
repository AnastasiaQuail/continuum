<?php

declare(strict_types=1);

namespace Continuum\Repository;

use Continuum\Entity\CalendarDay;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CalendarDay>
 */
final class CalendarDayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CalendarDay::class);
    }

    /**
     * @return list<CalendarDay>
     */
    public function findBetweenDates(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        return $this->createQueryBuilder('cd')
            ->andWhere('cd.date BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->addOrderBy('cd.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<CalendarDay>
     */
    public function findByYear(int $year): array
    {
        return $this->findBetweenDates(
            from: new DateTimeImmutable(sprintf('%s-01-01', $year)),
            to: new DateTimeImmutable(sprintf('%s-12-31', $year))
        );
    }
}
