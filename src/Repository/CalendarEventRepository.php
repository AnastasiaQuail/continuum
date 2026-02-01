<?php

declare(strict_types=1);

namespace Continuum\Repository;

use Continuum\Entity\CalendarEvent;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CalendarEvent>
 */
final class CalendarEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CalendarEvent::class);
    }

    /**
     * @return list<CalendarEvent>
     */
    private function findBetweenDates(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        return $this->createQueryBuilder('cd')
            ->andWhere('cd.datetime BETWEEN :from AND :to')
            ->setParameter('from', $from->setTimezone(new DateTimeZone('UTC')))
            ->setParameter('to', $to->setTimezone(new DateTimeZone('UTC')))
            ->addOrderBy('cd.datetime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<CalendarEvent>
     */
    public function findUpcomingNextDays(int $days, DateTimeZone $timeZone): array
    {
        return $this->findBetweenDates(
            new DateTimeImmutable('now', $timeZone)->setTime(0, 0),
            new DateTimeImmutable(sprintf('+%d days', $days - 1), $timeZone)->setTime(23, 59, 59),
        );
    }

    /**
     * @return list<CalendarEvent>
     */
    public function findByYear(int $year, DateTimeZone $timeZone): array
    {
        return $this->findBetweenDates(
            new DateTimeImmutable(sprintf('%d-01-01 00:00:00', $year), $timeZone),
            new DateTimeImmutable(sprintf('%d-12-31 23:59:59', $year), $timeZone),
        );
    }

    /**
     * @return list<CalendarEvent>
     */
    public function findByDay(DateTimeImmutable $date, DateTimeZone $timeZone): array
    {
        $day = $date->format('Y-m-d');

        return $this->findBetweenDates(
            new DateTimeImmutable(sprintf('%s 00:00:00', $day), $timeZone),
            new DateTimeImmutable(sprintf('%s 23:59:59', $day), $timeZone),
        );
    }

    public function delete(CalendarEvent $event): void
    {
        $this->getEntityManager()->remove($event);
        $this->getEntityManager()->flush();
    }

    public function create(CalendarEvent $event): void
    {
        $this->getEntityManager()->persist($event);
        $this->getEntityManager()->flush();
    }
}
