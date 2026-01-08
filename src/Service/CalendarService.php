<?php

declare(strict_types=1);

namespace Continuum\Service;

use Continuum\Dto\Calendar\CombinedCalendarDay;
use Continuum\Dto\Calendar\UpcomingNotification;
use Continuum\Entity\CalendarDay;
use Continuum\Entity\User;
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
    public function getUpcomingNotifications(User $user): array
    {
        $currentDate = new DateTimeImmutable();
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
            $text = $this->getUpcomingNotificationText($user, $calendarDay);

            if ($text !== null) {
                $notifications[] = new UpcomingNotification(
                    type: $calendarDay->getType(),
                    title: $calendarDay->getTitle(),
                    text: $text,
                );
            }
        }

        return $notifications;
    }

    private function getUpcomingNotificationText(User $user, CalendarDay $calendarDay): ?string
    {
        $currentDate = new DateTimeImmutable('now', $user->getTimezone());
        $calendarDate = $calendarDay->getDate();

        if ($calendarDay->isEvent()) {
            $calendarDate = $calendarDate
                ->setTime(
                    (int) $calendarDay->getTime()->format('H'),
                    (int) $calendarDay->getTime()->format('i'),
                    (int) $calendarDay->getTime()->format('s')
                )
                ->setTimezone($user->getTimezone());
        } else {
            $calendarDate = $calendarDate
                ->setTimezone($user->getTimezone())
                ->setTime(0, 0);
        }

        if ($calendarDate < $currentDate) {
            return null;
        }

        $days = $currentDate->diff($calendarDate)->days;

        if ($days >= 7) {
            $weeks = round($days / 7);

            return $weeks > 1 ? sprintf('in %d weeks', $weeks) : 'in a week';
        }

        if (!$calendarDay->isEvent()) {
            if ($days >= 1) {
                return $days > 1 ? sprintf('in %d days', $days) : 'in a day';
            }

            return $currentDate->format('d') !== $calendarDate->format('d') ? 'tomorrow' : 'today';
        }

        $time = $calendarDate->format('H:i');

        if ($days >= 1) {
            return $days > 1 ? sprintf('in %d days at %s', $days, $time) : sprintf('in a day at %s', $time);
        }

        return $currentDate->format('d') !== $calendarDate->format('d')
            ? sprintf('tomorrow at %s', $time)
            : sprintf('today at %s', $time);
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
            static fn ($day) => new CombinedCalendarDay($day['type'], $day['events']),
            $dayData
        );
    }
}
