<?php

declare(strict_types=1);

namespace Continuum\Service;

use Continuum\Dto\Request\Calendar\NewCalendarEvent;
use Continuum\Dto\Response\Calendar\CombinedCalendarEvent;
use Continuum\Entity\CalendarEvent;
use Continuum\Entity\User;
use Continuum\Enum\CalendarEventFormat;
use Continuum\Repository\CalendarEventRepository;
use DateTimeImmutable;
use DateTimeZone;

final readonly class CalendarEventService
{
    private const int NEXT_DAYS = 21;

    public function __construct(
        private CalendarEventRepository $calendarEventRepository,
    ) {}

    /**
     * @return list<CalendarEvent>
     */
    public function getByNextDays(User $user): array
    {
        return $this->calendarEventRepository->findUpcomingNextDays(self::NEXT_DAYS, $user->getTimezone());
    }

    /**
     * @return array<CombinedCalendarEvent>
     */
    public function getByYear(User $user, int $year): array
    {
        $events = [];

        foreach ($this->calendarEventRepository->findByYear($year, $user->getTimezone()) as $event) {
            $date = $event->getDatetime()->setTimezone($user->getTimezone())->format('Y-m-d');

            if (!array_key_exists($date, $events)) {
                $events[$date] = [
                    'day' => null,
                    'hours' => [],
                ];
            }

            if ($event->isAllDay()) {
                $events[$date]['day'] = $event;
            } else {
                $events[$date]['hours'][] = $event;
            }
        }

        return array_map(
            static fn ($day) => new CombinedCalendarEvent($day['day'], $day['hours']),
            $events
        );
    }

    /**
     * @return array<CalendarEvent>
     */
    public function getByDay(User $user, DateTimeImmutable $date): array
    {
        return $this->calendarEventRepository->findByDay($date, $user->getTimezone());
    }

    public function delete(CalendarEvent $event): void
    {
        $this->calendarEventRepository->delete($event);
    }

    public function create(User $user, DateTimeImmutable $date, NewCalendarEvent $dto): CalendarEvent
    {
        $dateTime = new DateTimeImmutable(
            sprintf('%s %d:%d:00', $date->format('Y-m-d'), $dto->time?->format('H'), (int) $dto->time?->format('i')),
            $user->getTimezone()
        );

        $event = new CalendarEvent(
            datetime: $dateTime->setTimezone(new DateTimeZone('UTC')),
            format: $dto->time === null ? CalendarEventFormat::Day : CalendarEventFormat::Hour,
            type: $dto->type,
            title: $dto->title,
        );

        $this->calendarEventRepository->create($event);

        return $event;
    }
}
