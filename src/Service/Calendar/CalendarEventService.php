<?php

declare(strict_types=1);

namespace Continuum\Service\Calendar;

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
    public function __construct(
        private CalendarEventRepository $repository,
    ) {}

    /**
     * @return list<CalendarEvent>
     */
    public function getByNextDays(User $user, int $days): array
    {
        return $this->repository->findUpcomingNextDays($days, $user->timezone);
    }

    /**
     * @return array<non-empty-string, CombinedCalendarEvent>
     */
    public function getByYear(User $user, int $year): array
    {
        $events = [];

        foreach ($this->repository->findByYear($year, $user->timezone) as $event) {
            $date = $event->getDatetime()->setTimezone($user->timezone)->format('Y-m-d');

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
            static fn (array $day): CombinedCalendarEvent => new CombinedCalendarEvent($day['day'], $day['hours']),
            $events
        );
    }

    /**
     * @return list<CalendarEvent>
     */
    public function getByDay(User $user, DateTimeImmutable $date): array
    {
        return $this->repository->findByDay($date, $user->timezone);
    }

    public function delete(CalendarEvent $event): void
    {
        $this->repository->delete($event);
    }

    public function create(User $user, DateTimeImmutable $date, NewCalendarEvent $dto): CalendarEvent
    {
        $dateTime = new DateTimeImmutable(
            sprintf('%s %s:%s:00', $date->format('Y-m-d'), $dto->time?->format('H'), $dto->time?->format('i') ?? '00'),
            $user->timezone
        );

        $event = new CalendarEvent(
            datetime: null === $dto->time ? new DateTimeImmutable($dateTime->format('Y-m-d H:i:s'))
                : $dateTime->setTimezone(new DateTimeZone('UTC')),
            format: null === $dto->time ? CalendarEventFormat::Day : CalendarEventFormat::Hour,
            type: $dto->type,
            title: $dto->title,
        );

        $this->repository->create($event);

        return $event;
    }
}
