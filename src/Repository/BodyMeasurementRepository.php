<?php

declare(strict_types=1);

namespace Continuum\Repository;

use Continuum\Entity\BodyMeasurement;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BodyMeasurement>
 */
final class BodyMeasurementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BodyMeasurement::class);
    }

    /**
     * @return list<BodyMeasurement>
     */
    private function findBetweenDates(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        return $this->createQueryBuilder('bm')
            ->andWhere('bm.datetime BETWEEN :from AND :to')
            ->setParameter('from', $from->setTimezone(new DateTimeZone('UTC')))
            ->setParameter('to', $to->setTimezone(new DateTimeZone('UTC')))
            ->addOrderBy('bm.datetime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<BodyMeasurement>
     */
    public function findByMonth(int $year, int $month, DateTimeZone $timeZone): array
    {
        return $this->findBetweenDates(
            new DateTimeImmutable(sprintf('%d-%d-01 00:00:00', $year, $month), $timeZone),
            new DateTimeImmutable(sprintf('%d-%d-31 23:59:59', $year, $month), $timeZone),
        );
    }

    public function save(BodyMeasurement $measurement): void
    {
        $this->getEntityManager()->persist($measurement);
        $this->getEntityManager()->flush();
    }
}
