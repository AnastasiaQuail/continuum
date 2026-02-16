<?php

declare(strict_types=1);

namespace Continuum\Repository;

use Continuum\Entity\WeeklyReflection;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WeeklyReflection>
 */
final class WeeklyReflectionRepository extends ServiceEntityRepository implements WeeklyReflectionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WeeklyReflection::class);
    }

    /**
     * @return list<WeeklyReflection>
     */
    public function findByDays(DateTimeImmutable ...$days): array
    {
        return $this->createQueryBuilder('wr')
            ->andWhere('wr.date IN (:days)')
            ->setParameter(
                'days',
                array_map(static fn (DateTimeImmutable $day): string => $day->format('Y-m-d'), $days)
            )
            ->addOrderBy('wr.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByDay(DateTimeImmutable $day): ?WeeklyReflection
    {
        return $this->findOneBy(['date' => $day]);
    }

    public function save(WeeklyReflection $weeklyReflection): void
    {
        $this->getEntityManager()->persist($weeklyReflection);
        $this->getEntityManager()->flush();
    }
}
