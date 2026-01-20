<?php

declare(strict_types=1);

namespace Continuum\Controller\Calendar;

use Continuum\Entity\User;
use Continuum\Security\Authorization\Voter\CalendarVoter;
use Continuum\Service\CalendarEventService;
use Continuum\Service\UpcomingEventService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CalendarController extends AbstractController
{
    public function __construct(
        #[Autowire(env: 'string:APP_DATE_START')]
        private readonly string $startDate,
        private readonly UpcomingEventService $upcomingEventService,
        private readonly CalendarEventService $calendarEventService,
    ) {}

    #[Route(path: '/calendar/{year}', name: 'app_calendar', requirements: ['year' => '\d+'], methods: ['GET'])]
    #[IsGranted(CalendarVoter::VIEW)]
    public function __invoke(#[CurrentUser] User $user, ?int $year = null): Response
    {
        $year ??= (int) new DateTimeImmutable('now', $user->getTimezone())->format('Y');
        $startDay = new DateTimeImmutable($this->startDate, $user->getTimezone());

        $upcomingNotifications = $this->upcomingEventService->getUpcomingNotifications($user);
        $events = $this->calendarEventService->getByYear($user, $year);

        return $this->render('calendar/index.html.twig', [
            'year' => $year,
            'startDay' => $startDay,
            'upcomingNotifications' => $upcomingNotifications,
            'events' => $events,
        ]);
    }
}
