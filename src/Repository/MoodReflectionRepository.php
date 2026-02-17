<?php

declare(strict_types=1);

namespace Continuum\Repository;

use Continuum\Entity\MoodReflection;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MoodReflection>
 */
final class MoodReflectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MoodReflection::class);
    }

    /**
     * @return list<MoodReflection>
     */
    public function findPreviousDays(int $days): array
    {
        return $this->findBetweenDates(
            new DateTimeImmutable(sprintf('-%d days', $days - 1)),
            new DateTimeImmutable('now'),
        );
    }

    /**
     * @return list<MoodReflection>
     */
    public function findByMonth(DateTimeImmutable $month): array
    {
        return $this->findBetweenDates(
            new DateTimeImmutable(
                sprintf('%s-%s-01 00:00:00', $month->format('Y'), $month->format('m'))
            ),
            new DateTimeImmutable(
                sprintf('%s-%s-%s 23:59:59', $month->format('Y'), $month->format('m'), $month->format('t'))
            ),
        );
    }

    public function findOneByDay(DateTimeImmutable $day): ?MoodReflection
    {
        return $this->findOneBy(['date' => $day]);
    }

    public function save(MoodReflection $moodReflection): void
    {
        $this->getEntityManager()->persist($moodReflection);
        $this->getEntityManager()->flush();
    }

    /**
     * @return list<MoodReflection>
     */
    private function findBetweenDates(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        return $this->createQueryBuilder('rm')
            ->andWhere('rm.date BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->addOrderBy('rm.date', Order::Ascending->value)
            ->getQuery()
            ->getResult();
    }
}
