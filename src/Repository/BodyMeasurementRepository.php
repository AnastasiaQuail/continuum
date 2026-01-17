<?php

declare(strict_types=1);

namespace Continuum\Repository;

use Continuum\Entity\BodyMeasurement;
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

    public function save(BodyMeasurement $measurement): void
    {
        $this->getEntityManager()->persist($measurement);
        $this->getEntityManager()->flush();
    }
}
