<?php

declare(strict_types=1);

namespace Continuum\Controller\Calendar;

use Continuum\Service\CalendarService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CalendarController extends AbstractController
{
    public function __construct(
        private readonly CalendarService $calendarService,
    ) {}

    #[Route(path: '/calendar/{year}', name: 'app_calendar')]
    public function __invoke(?int $year = null): Response
    {
        $year ??= (int) date('Y');
        $startDay = new DateTimeImmutable('2025-03-15');

        $upcomingNotifications = $this->calendarService->getUpcomingNotifications();
        $days = $this->calendarService->getDaysByYear($year);

        return $this->render('calendar/index.html.twig', [
            'year' => $year,
            'startDay' => $startDay,
            'upcomingNotifications' => $upcomingNotifications,
            'days' => $days,
        ]);
    }
}
