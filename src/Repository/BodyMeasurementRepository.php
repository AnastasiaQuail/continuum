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

        return $this->createQueryBuilder('bm')
            ->andWhere('bm.datetime BETWEEN :from AND :to')
            ->setParameter('from', $from->setTimezone(new DateTimeZone('UTC')))
            ->setParameter('to', $to->setTimezone(new DateTimeZone('UTC')))
            ->addOrderBy('bm.datetime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneLastByMonth(DateTimeImmutable $month, DateTimeZone $timeZone): ?BodyMeasurement
    {
        $from = new DateTimeImmutable(
            sprintf('%d-%d-01 00:00:00', $month->format('Y'), $month->format('m')),
            $timeZone
        );
        $to = new DateTimeImmutable(
            sprintf('%d-%d-%d 23:59:59', $month->format('Y'), $month->format('m'), $month->format('t')),
            $timeZone
        );

        return $this->createQueryBuilder('bm')
            ->andWhere('bm.datetime BETWEEN :from AND :to')
            ->setParameter('from', $from->setTimezone(new DateTimeZone('UTC')))
            ->setParameter('to', $to->setTimezone(new DateTimeZone('UTC')))
            ->addOrderBy('bm.datetime', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findLast(int $limit): array
    {
        return $this->findBy([], ['datetime' => 'ASC'], $limit);
    }

    public function save(BodyMeasurement $measurement): void
    {
        $this->getEntityManager()->persist($measurement);
        $this->getEntityManager()->flush();
    }
}
