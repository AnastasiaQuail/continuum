<?php

declare(strict_types=1);

namespace Continuum\Repository;

use Continuum\Entity\ReflectionMood;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReflectionMood>
 */
final class ReflectionMoodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReflectionMood::class);
    }

    /**
     * @return list<ReflectionMood>
     */
    private function findBetweenDates(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        return $this->createQueryBuilder('rm')
            ->andWhere('rm.date BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->addOrderBy('rm.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<ReflectionMood>
     */
    public function findPreviousDays(int $days): array
    {
        return $this->findBetweenDates(
            new DateTimeImmutable(sprintf('-%d days', $days - 1)),
            new DateTimeImmutable('now'),
        );
    }

    /**
     * @return list<ReflectionMood>
     */
    public function findByMonth(int $year, int $month): array
    {
        return $this->findBetweenDates(
            new DateTimeImmutable(sprintf('%d-%d-01 00:00:00', $year, $month)),
            new DateTimeImmutable(sprintf('%d-%d-31 23:59:59', $year, $month)),
        );
    }

    public function findOneByDay(DateTimeImmutable $day): ?ReflectionMood
    {
        return $this->findOneBy(['date' => $day]);
    }

    public function save(ReflectionMood $event): void
    {
        $this->getEntityManager()->persist($event);
        $this->getEntityManager()->flush();
    }
}
