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
    public function findByYear(int $year): array
    {
        return $this->createQueryBuilder('cd')
            ->andWhere('cd.date BETWEEN :from AND :to')
            ->setParameter('from', new DateTimeImmutable(sprintf('%s-01-01', $year)))
            ->setParameter('to', new DateTimeImmutable(sprintf('%s-12-31', $year)))
            ->getQuery()
            ->getResult();
    }
}
