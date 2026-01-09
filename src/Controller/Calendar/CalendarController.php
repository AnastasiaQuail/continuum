<?php

declare(strict_types=1);

namespace Continuum\Controller\Calendar;

use Continuum\Entity\User;
use Continuum\Service\CalendarService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class CalendarController extends AbstractController
{
    public function __construct(
        private readonly CalendarService $calendarService,
    ) {}

    #[Route(path: '/calendar/{year}', name: 'app_calendar', methods: ['GET'])]
    public function __invoke(#[CurrentUser] User $user, ?int $year = null): Response
    {
        $year ??= (int) new DateTimeImmutable('now', $user->getTimezone())->format('Y');
        $startDay = new DateTimeImmutable('2025-03-15');

        $upcomingNotifications = $this->calendarService->getUpcomingNotifications($user);
        $days = $this->calendarService->getDaysByYear($year);

        return $this->render('calendar/index.html.twig', [
            'year' => $year,
            'startDay' => $startDay,
            'upcomingNotifications' => $upcomingNotifications,
            'days' => $days,
        ]);
    }
}
