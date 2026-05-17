<?php

declare(strict_types=1);

namespace Continuum\Service\Calendar;

use Continuum\Dto\Response\Calendar\UpcomingEvent;
use Continuum\Entity\CalendarEvent;
use Continuum\Entity\User;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class UpcomingEventService
{
    public function __construct(
        #[Autowire(param: 'app.calendar.upcoming_events.days')]
        private int $upcomingEventsDays,
        private CalendarEventService $calendarEventService,
    ) {}

    /**
     * @return list<UpcomingEvent>
     */
    public function getClosestEvents(User $user): array
    {
        return $this->getEvents($user, days: 3);
    }

    /**
     * @return list<UpcomingEvent>
     */
    public function getEvents(User $user, ?int $days = null): array
    {
        $events = $this->calendarEventService->getByNextDays($user, $days ?? $this->upcomingEventsDays);
        $data = $this->getUniqueEvents($events);

        return $this->getUpcomingEvents($user, $data);
    }

    /**
     * @param list<CalendarEvent> $events
     *
     * @return list<CalendarEvent>
     */
    private function getUniqueEvents(array $events): array
    {
        /** @var array<string, CalendarEvent> $dayEvents */
        $dayEvents = [];

        /** @var array<string, CalendarEvent> $hourEvents */
        $hourEvents = [];

        foreach ($events as $event) {
            $type = $event->type->value;

            if ($event->isAllDay()) {
                if (!array_key_exists($type, $dayEvents)) {
                    $dayEvents[$type] = $event;
                }
            } elseif (!array_key_exists($type, $hourEvents)) {
                $hourEvents[$type] = $event;
            }
        }

        /** @var array<string, CalendarEvent> $data */
        $data = [...$hourEvents, ...$dayEvents];
        usort($data, static fn (CalendarEvent $a, CalendarEvent $b): int => $a->datetime <=> $b->datetime);

        return $data;
    }

    /**
     * @param list<CalendarEvent> $events
     *
     * @return list<UpcomingEvent>
     */
    private function getUpcomingEvents(User $user, array $events): array
    {
        $upcomingEvents = [];

        foreach ($events as $event) {
            if (null !== $text = $this->getUpcomingText($user, $event)) {
                $upcomingEvents[] = new UpcomingEvent(
                    type: $event->type,
                    title: $event->title,
                    text: $text,
                );
            }
        }

        return $upcomingEvents;
    }

    /**
     * @return null|non-empty-string
     */
    private function getUpcomingText(User $user, CalendarEvent $event): ?string
    {
        $eventDate = $event->getUserDatetime($user);
        $currentDate = new DateTimeImmutable('now', $user->timezone);

        if (
            $eventDate < $currentDate
            && (
                !$event->isAllDay()
                || $currentDate->setTime(0, 0) < $eventDate->setTime(0, 0)
            )
        ) {
            return null;
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
            $weeks = (int) round($days / 7);

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
