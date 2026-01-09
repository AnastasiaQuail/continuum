<?php

declare(strict_types=1);

namespace Continuum\Command;

use Continuum\Entity\CalendarDay;
use Continuum\Entity\CalendarEvent;
use Continuum\Enum\CalendarEventFormat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:calendar:days:migrate',
    description: 'Migrate calendar days to new table',
)]
final readonly class CalendarDaysMigrationCommand
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function __invoke(SymfonyStyle $io): int
    {
        $calendarDays = $this->entityManager->getRepository(CalendarDay::class)->findAll();

        foreach ($calendarDays as $calendarDay) {
            $datetime = $calendarDay->getDate();

            if ($calendarDay->isEvent()) {
                $datetime = $datetime->setTime(
                    (int) $calendarDay->getTime()->format('H'),
                    (int) $calendarDay->getTime()->format('i'),
                    (int) $calendarDay->getTime()->format('s')
                );
            }

            $calendarEvent = new CalendarEvent(
                datetime: $datetime,
                format: $calendarDay->isEvent() ? CalendarEventFormat::Hour : CalendarEventFormat::Day,
                type: $calendarDay->getType(),
                title: $calendarDay->getTitle(),
            );

            $this->entityManager->persist($calendarEvent);
        }

        $this->entityManager->flush();

        $io->success('All calendar events have been migrated');

        return Command::SUCCESS;
    }
}
