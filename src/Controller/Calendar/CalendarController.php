<?php

declare(strict_types=1);

namespace Continuum\Controller\Calendar;

use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CalendarController extends AbstractController
{
    #[Route(path: '/calendar/{year}', name: 'app_calendar')]
    public function __invoke(?int $year = null): Response
    {
        $year ??= date('Y');
        $startDay = new DateTimeImmutable('2020-10-03');
        $events = [];

        return $this->render('calendar/index.html.twig', [
            'year' => $year,
            'startDay' => $startDay,
            'events' => $events,
        ]);
    }
}
