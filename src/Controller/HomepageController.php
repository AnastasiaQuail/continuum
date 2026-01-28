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
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
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
    public function __invoke(
        #[CurrentUser] User $user,
        #[MapQueryParameter('measurement', options: ['min_range' => 1])] int $measurementMonths = 2,
        #[MapQueryParameter('workout', options: ['min_range' => 1])] int $workoutMonths = 2,
    ): Response {
        $date = new DateTimeImmutable('now', $user->getTimezone())->setTime(0, 0);

        $measurementDays = 30 * $measurementMonths;
        $prevDate = $date->modify(sprintf('-%d days', $measurementDays));
        $measurements = $this->measurementService->getByRange($user, $prevDate, $date);
        $chartMeasurements = $this->chartMeasurementService->getChartMeasurements($user, $prevDate, $measurements);

        $workoutDays = round((30 * $workoutMonths) / 7) * 7 + (int) $date->format('N');
        $prevDate = $date->modify(sprintf('-%d days', $workoutDays));
        $workouts = $this->workoutService->getByRange($user, $prevDate, $date);

        return $this->render('default/homepage.html.twig', [
            'calendarProgress' => $this->calendarProgressService->getCurrentProgress(),
            'chartMeasurements' => $chartMeasurements,
            'measurementDays' => $measurementDays,
            'workouts' => $workouts,
            'workoutDays' => $workoutDays,
        ]);
    }
}
