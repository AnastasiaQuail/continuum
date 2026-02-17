<?php

declare(strict_types=1);

namespace Continuum\Repository;

use Continuum\Dto\Response\Measurement\LastMeasurement;
use Continuum\Entity\BodyMeasurement;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Order;
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
    public function findByRange(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        return $this->createQueryBuilder('bm')
            ->andWhere('bm.datetime BETWEEN :from AND :to')
            ->setParameter('from', $from->setTimezone(new DateTimeZone('UTC')))
            ->setParameter('to', $to->setTimezone(new DateTimeZone('UTC')))
            ->addOrderBy('bm.datetime', Order::Ascending->value)
            ->getQuery()
            ->getResult();
    }

    public function findOneLastByMonth(DateTimeImmutable $month, DateTimeZone $timeZone): ?BodyMeasurement
    {
        $from = new DateTimeImmutable(
            sprintf('%s-%s-01 00:00:00', $month->format('Y'), $month->format('m')),
            $timeZone
        );
        $to = new DateTimeImmutable(
            sprintf('%s-%s-%s 23:59:59', $month->format('Y'), $month->format('m'), $month->format('t')),
            $timeZone
        );

        $result = $this->createQueryBuilder('bm')
            ->andWhere('bm.datetime BETWEEN :from AND :to')
            ->setParameter('from', $from->setTimezone(new DateTimeZone('UTC')))
            ->setParameter('to', $to->setTimezone(new DateTimeZone('UTC')))
            ->addOrderBy('bm.datetime', Order::Descending->value)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        assert(null === $result || $result instanceof BodyMeasurement);

        return $result;
    }

    public function findOneLastWithNotNull(): LastMeasurement
    {
        $sql = <<<'SQL'
            SELECT
                (SELECT weight FROM body_measurements ORDER BY datetime DESC LIMIT 1),
                (SELECT neck FROM body_measurements WHERE neck IS NOT NULL ORDER BY datetime DESC LIMIT 1),
                (SELECT chest FROM body_measurements WHERE chest IS NOT NULL ORDER BY datetime DESC LIMIT 1),
                (SELECT shoulders FROM body_measurements WHERE shoulders IS NOT NULL ORDER BY datetime DESC LIMIT 1),
                (SELECT waist FROM body_measurements WHERE waist IS NOT NULL ORDER BY datetime DESC LIMIT 1),
                (SELECT flexed_biceps FROM body_measurements WHERE flexed_biceps IS NOT NULL ORDER BY datetime DESC LIMIT 1),
                (SELECT hips FROM body_measurements WHERE hips IS NOT NULL ORDER BY datetime DESC LIMIT 1),
                (SELECT thigh FROM body_measurements WHERE thigh IS NOT NULL ORDER BY datetime DESC LIMIT 1),
                (SELECT calf FROM body_measurements WHERE calf IS NOT NULL ORDER BY datetime DESC LIMIT 1)
            FROM body_measurements
            LIMIT 1
            SQL;

        /** @var array<string, int> $row */
        $row = $this->getEntityManager()->getConnection()
            ->executeQuery($sql)
            ->fetchAssociative();

        foreach ($row as $field => $value) {
            $row[$field] = round($value / ('weight' === $field ? 1000 : 10), 1);
        }

        return new LastMeasurement(...array_values($row));
    }

    public function save(BodyMeasurement $measurement): void
    {
        $this->getEntityManager()->persist($measurement);
        $this->getEntityManager()->flush();
    }
}
