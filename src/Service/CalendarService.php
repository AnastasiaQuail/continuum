<?php

declare(strict_types=1);

namespace Continuum\Service;

use Continuum\Dto\Calendar\CombinedCalendarDay;
use Continuum\Dto\Calendar\UpcomingNotification;
use Continuum\Entity\CalendarDay;
use Continuum\Repository\CalendarDayRepository;
use DateTimeImmutable;

final readonly class CalendarService
{
    public function __construct(
        private CalendarDayRepository $calendarDayRepository,
    ) {}

    /**
     * @return list<UpcomingNotification>
     */
    public function getUpcomingNotifications(): array
    {
        $currentDate = new DateTimeImmutable()->setTime(0, 0);
        $endDate = new DateTimeImmutable('+21 days');

        $calendarDays = $this->calendarDayRepository->findBetweenDates($currentDate, $endDate);

        /** @var array<string, CalendarDay> $types */
        $types = [];
        /** @var array<string, CalendarDay> $events */
        $events = [];

        foreach ($calendarDays as $calendarDay) {
            $type = $calendarDay->getType()->value;

            if ($calendarDay->isEvent()) {
                if (!array_key_exists($type, $events)) {
                    $events[$type] = $calendarDay;
                }
            } elseif (!array_key_exists($type, $types)) {
                $types[$type] = $calendarDay;
            }
        }

        /** @var array<CalendarDay> $data */
        $data = [...$events, ...$types];
        usort($data, static fn (CalendarDay $a, CalendarDay $b) => $a->getDate() <=> $b->getDate());

        $notifications = [];

        foreach ($data as $calendarDay) {
            $dateDiff = $currentDate->diff($calendarDay->getDate());
            $text = $this->getUpcomingNotificationText($calendarDay, $dateDiff->days);

            $notifications[] = new UpcomingNotification(
                type: $calendarDay->getType(),
                title: $calendarDay->getTitle(),
                text: $text,
            );
        }

        return $notifications;
    }

    private function getUpcomingNotificationText(CalendarDay $calendarDay, int $days): string
    {
        if ($days >= 7) {
            $weeks = round($days / 7);

            return $weeks > 1 ? sprintf('in %d weeks', $weeks) : 'in a week';
        }

        if ($days >= 2) {
            return sprintf('in %d days', $days);
        }

        $time = $calendarDay->isEvent() ? $calendarDay->getTime()->format('H:s') : null;

        if ($days === 1) {
            return $time !== null ? sprintf('tomorrow at %s', $time) : 'tomorrow';
        }

        return $time !== null ? sprintf('today at %s', $time) : 'today';
    }

    /**
     * @return array<CombinedCalendarDay>
     */
    public function getDaysByYear(int $year): array
    {
        $dayData = [];

        foreach ($this->calendarDayRepository->findByYear($year) as $calendarDay) {
            $date = $calendarDay->getDate()->format('Y-m-d');

            if (!array_key_exists($date, $dayData)) {
                $dayData[$date] = [
                    'type' => null,
                    'events' => [],
                ];
            }

            if ($calendarDay->isEvent()) {
                $dayData[$date]['events'][] = $calendarDay;
            } else {
                $dayData[$date]['type'] = $calendarDay;
            }
        }

        return array_map(
            static fn ($day) => new CombinedCalendarDay($day['type'], $day['events'],),
            $dayData
        );
    }
}
