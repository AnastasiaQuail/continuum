<?php

declare(strict_types=1);

namespace Continuum\Controller;

use Continuum\Entity\User;
use Continuum\Service\Calendar\CalendarProgressService;
use Continuum\Service\Measurement\ChartMeasurementService;
use Continuum\Service\Measurement\MeasurementService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class HomepageController extends AbstractController
{
    public function __construct(
        private readonly CalendarProgressService $calendarProgressService,
        private readonly MeasurementService $measurementService,
        private readonly ChartMeasurementService $chartMeasurementService,
    ) {}

    #[Route(path: '/', name: 'app_homepage')]
    public function __invoke(#[CurrentUser] User $user): Response
    {
        $currentDate = new DateTimeImmutable('now', $user->getTimezone())->setTime(0, 0);
        $prevDate = $currentDate->modify('-90 days');

        $measurements = $this->measurementService->getByMonths($user, $prevDate, $currentDate);
        $chartMeasurements = $this->chartMeasurementService->getChartMeasurements($user, $prevDate, $measurements);

        return $this->render('default/homepage.html.twig', [
            'calendarProgress' => $this->calendarProgressService->getCurrentProgress(),
            'chartMeasurements' => $chartMeasurements,
        ]);
    }
}
