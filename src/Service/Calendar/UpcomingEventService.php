<?php

declare(strict_types=1);

namespace Continuum\Service\Calendar;

use Continuum\Dto\Response\Calendar\UpcomingNotification;
use Continuum\Entity\CalendarEvent;
use Continuum\Entity\User;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class UpcomingEventService
{
    public function __construct(
        #[Autowire(param: 'app.calendar.upcoming_notification.days')]
        private int $upcomingNotificationDays,
        private CalendarEventService $calendarEventService,
    ) {}

    /**
     * @return list<UpcomingNotification>
     */
    public function getUpcomingNotifications(User $user): array
    {
        $events = $this->calendarEventService->getByNextDays($user, $this->upcomingNotificationDays);

        /** @var array<string, CalendarEvent> $dayEvents */
        $dayEvents = [];
        /** @var array<string, CalendarEvent> $hourEvents */
        $hourEvents = [];

        foreach ($events as $event) {
            $type = $event->getType()->value;

            if ($event->isAllDay()) {
                if (!array_key_exists($type, $dayEvents)) {
                    $dayEvents[$type] = $event;
                }
            } elseif (!array_key_exists($type, $hourEvents)) {
                $hourEvents[$type] = $event;
            }
        }

        /** @var array<CalendarEvent> $data */
        $data = [...$hourEvents, ...$dayEvents];
        usort($data, static fn (CalendarEvent $a, CalendarEvent $b) => $a->getDatetime() <=> $b->getDatetime());

        $notifications = [];

        foreach ($data as $event) {
            $text = $this->getUpcomingText($user, $event);

            if ($text !== null) {
                $notifications[] = new UpcomingNotification(
                    type: $event->getType(),
                    title: $event->getTitle(),
                    text: $text,
                );
            }
        }

        return $notifications;
    }

    /**
     * @return non-empty-string|null
     */
    private function getUpcomingText(User $user, CalendarEvent $event): ?string
    {
        $currentDate = new DateTimeImmutable('now', $user->getTimezone());
        $eventDate = $event->getDatetime()->setTimezone($user->getTimezone());

        if ($eventDate < $currentDate) {
            if (
                !$event->isAllDay()
                || ($currentDate->setTime(0, 0) < $eventDate->setTime(0, 0))
            ) {
                return null;
            }
        }

        return $this->getText($currentDate, $eventDate, $event->isAllDay());
    }

    /**
     * @return non-empty-string
     */
    private function getText(DateTimeImmutable $currentDate, DateTimeImmutable $eventDate, bool $isAllDayEvent): string
    {
        $days = $currentDate->diff($eventDate)->days;

        if ($days >= 7) {
            $weeks = round($days / 7);

            return $weeks > 1 ? sprintf('in %d weeks', $weeks) : 'in a week';
        }

        if ($isAllDayEvent) {
            if ($days >= 1) {
                return $days > 1 ? sprintf('in %d days', $days) : 'in a day';
            }

            return $currentDate->format('d') !== $eventDate->format('d') ? 'tomorrow' : 'today';
        }

        $time = $eventDate->format('H:i');

        if ($days >= 1) {
            return $days > 1 ? sprintf('in %d days at %s', $days, $time) : sprintf('in a day at %s', $time);
        }

        return $currentDate->format('d') !== $eventDate->format('d')
            ? sprintf('tomorrow at %s', $time)
            : sprintf('today at %s', $time);
    }
}
