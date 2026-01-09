<?php

declare(strict_types=1);

namespace Continuum\Service;

use Continuum\Dto\Calendar\CombinedCalendarEvent;
use Continuum\Dto\Calendar\UpcomingNotification;
use Continuum\Entity\CalendarEvent;
use Continuum\Entity\User;
use Continuum\Repository\CalendarEventRepository;
use DateTimeImmutable;

final readonly class CalendarService
{
    public function __construct(
        private CalendarEventRepository $calendarEventRepository,
    ) {}

    /**
     * @return list<UpcomingNotification>
     */
    public function getUpcomingNotifications(User $user): array
    {
        $calendarEvents = $this->calendarEventRepository->findUpcomingByNextDays(21, $user->getTimezone());

        /** @var array<string, CalendarEvent> $dayEvents */
        $dayEvents = [];
        /** @var array<string, CalendarEvent> $hourEvents */
        $hourEvents = [];

        foreach ($calendarEvents as $calendarEvent) {
            $type = $calendarEvent->getType()->value;

            if ($calendarEvent->isAllDay()) {
                if (!array_key_exists($type, $dayEvents)) {
                    $dayEvents[$type] = $calendarEvent;
                }
            } elseif (!array_key_exists($type, $hourEvents)) {
                $hourEvents[$type] = $calendarEvent;
            }
        }

        /** @var array<CalendarEvent> $data */
        $data = [...$hourEvents, ...$dayEvents];
        usort($data, static fn (CalendarEvent $a, CalendarEvent $b) => $a->getDatetime() <=> $b->getDatetime());

        $notifications = [];

        foreach ($data as $calendarEvent) {
            $text = $this->getUpcomingNotificationText($user, $calendarEvent);

            if ($text !== null) {
                $notifications[] = new UpcomingNotification(
                    type: $calendarEvent->getType(),
                    title: $calendarEvent->getTitle(),
                    text: $text,
                );
            }
        }

        return $notifications;
    }

    private function getUpcomingNotificationText(User $user, CalendarEvent $calendarEvent): ?string
    {
        $currentDate = new DateTimeImmutable('now', $user->getTimezone());
        $calendarDate = $calendarEvent->getDatetime()->setTimezone($user->getTimezone());

        if ($calendarDate < $currentDate) {
            return null;
        }

        $days = $currentDate->diff($calendarDate)->days;

        if ($days >= 7) {
            $weeks = round($days / 7);

            return $weeks > 1 ? sprintf('in %d weeks', $weeks) : 'in a week';
        }

        if ($calendarEvent->isAllDay()) {
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
     * @return array<CombinedCalendarEvent>
     */
    public function getEventsByYear(User $user, int $year): array
    {
        $events = [];

        foreach ($this->calendarEventRepository->findByYear($year, $user->getTimezone()) as $calendarEvent) {
            $date = $calendarEvent->getDatetime()->setTimezone($user->getTimezone())->format('Y-m-d');

            if (!array_key_exists($date, $events)) {
                $events[$date] = [
                    'day' => null,
                    'hours' => [],
                ];
            }

            if ($calendarEvent->isAllDay()) {
                $events[$date]['day'] = $calendarEvent;
            } else {
                $events[$date]['hours'][] = $calendarEvent;
            }
        }

        return array_map(
            static fn ($day) => new CombinedCalendarEvent($day['day'], $day['hours']),
            $events
        );
    }
}
