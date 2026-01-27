<?php

declare(strict_types=1);

namespace Continuum\Controller;

use Continuum\Entity\User;
use Continuum\Service\Calendar\CalendarProgressService;
use Continuum\Service\Measurement\ChartMeasurementService;
use Continuum\Service\Measurement\MeasurementService;
use Continuum\Service\Workout\WorkoutService;
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
        private readonly WorkoutService $workoutService,
    ) {}

    #[Route(path: '/', name: 'app_homepage')]
    public function __invoke(#[CurrentUser] User $user): Response
    {
        $date = new DateTimeImmutable('now', $user->getTimezone())->setTime(0, 0);
        $showDays = 60;

        $prevDate = $date->modify(sprintf('-%d days', $showDays));
        $measurements = $this->measurementService->getByRange($user, $prevDate, $date);
        $chartMeasurements = $this->chartMeasurementService->getChartMeasurements($user, $prevDate, $measurements);

        $prevDate = $date->modify(sprintf('-%d days', round($showDays / 7) * 7 + (int) $date->format('N')));
        $workouts = $this->workoutService->getByRange($user, $prevDate, $date);

        return $this->render('default/homepage.html.twig', [
            'calendarProgress' => $this->calendarProgressService->getCurrentProgress(),
            'showDays' => $showDays,
            'chartMeasurements' => $chartMeasurements,
            'workouts' => $workouts,
        ]);
    }
}
