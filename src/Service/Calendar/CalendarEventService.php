<?php

declare(strict_types=1);

namespace Continuum\Service\Calendar;

use Continuum\Dto\Request\Calendar\NewCalendarEvent;
use Continuum\Dto\Response\Calendar\CombinedCalendarEvent;
use Continuum\Dto\Response\Calendar\ReportCalendarEvent;
use Continuum\Entity\CalendarEvent;
use Continuum\Entity\User;
use Continuum\Enum\CalendarEventFormat;
use Continuum\Enum\CalendarEventType;
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
     * @return list<CalendarEvent>
     */
    public function getByPreviousDays(User $user, DateTimeImmutable $date, int $days): array
    {
        return $this->repository->findPreviousDays($date, $days, $user->timezone);
    }

    /**
     * @return array<non-empty-string, CombinedCalendarEvent>
     */
    public function getByYear(User $user, int $year): array
    {
        /** @var array<non-empty-string, array{days: list<CalendarEvent>, hours: list<CalendarEvent>}> $events */
        $events = [];

        foreach ($this->repository->findByYear($year, $user->timezone) as $event) {
            $date = $event->getUserDatetime($user)->format('Y-m-d');

            if (!array_key_exists($date, $events)) {
                $events[$date] = [
                    'days' => [],
                    'hours' => [],
                ];
            }

            if ($event->isAllDay()) {
                $events[$date]['days'][] = $event;
            } else {
                $events[$date]['hours'][] = $event;
            }
        }

        return array_map(
            static function (array $day): CombinedCalendarEvent {
                $days = $day['days'];
                usort(
                    $days,
                    static fn (CalendarEvent $a, CalendarEvent $b): int => $a->getCreatedAt() <=> $b->getCreatedAt(),
                );

                return new CombinedCalendarEvent($days, $day['hours']);
            },
            $events
        );
    }

    /**
     * @return list<ReportCalendarEvent>
     */
    public function getCountMapBetweenDates(User $user, DateTimeImmutable $endDate): array
    {
        $data = [];

        foreach ($this->repository->findByMonth($endDate, $user->timezone) as $event) {
            $data[$event->title][$event->type->value] ??= 0;
            ++$data[$event->title][$event->type->value];
        }

        $events = [];

        /**
         * @var non-empty-string $title
         */
        foreach ($data as $title => $types) {
            /**
             * @var value-of<CalendarEventType> $type
             * @var positive-int $count
             */
            foreach ($types as $type => $count) {
                $events[] = new ReportCalendarEvent(
                    title: $title,
                    type: CalendarEventType::from($type),
                    count: $count,
                );
            }
        }

        usort($events, static fn (ReportCalendarEvent $a, ReportCalendarEvent $b): int => $b->count <=> $a->count);

        return $events;
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
            sprintf('%s %s:00', $date->format('Y-m-d'), $dto->time?->format('H:i') ?? '00:00'),
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
