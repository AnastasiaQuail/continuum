<?php

declare(strict_types=1);

namespace Continuum\Controller\Calendar;

use Continuum\Entity\CalendarDay;
use Continuum\Repository\CalendarDayRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CalendarController extends AbstractController
{
    public function __construct(
        private readonly CalendarDayRepository $calendarDayRepository,
    ) {}

    #[Route(path: '/calendar/{year}', name: 'app_calendar')]
    public function __invoke(?int $year = null): Response
    {
        $year ??= (int) date('Y');
        $startDay = new DateTimeImmutable('2025-03-15');

        /** @var list<string, CalendarDay> $days */
        $days = [];
        foreach ($this->calendarDayRepository->findByYear($year) as $calendarDay) {
            $days[$calendarDay->getDate()->format('Y-m-d')] = $calendarDay;
        }

        return $this->render('calendar/index.html.twig', [
            'year' => $year,
            'startDay' => $startDay,
            'days' => $days,
        ]);
    }
}
