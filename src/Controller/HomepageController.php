<?php

declare(strict_types=1);

namespace Continuum\Controller;

use Continuum\Entity\User;
use Continuum\Service\Calendar\CalendarProgressService;
use Continuum\Service\Calendar\UpcomingEventService;
use Continuum\Service\ChartMoodReflectionService;
use Continuum\Service\HolidayService;
use Continuum\Service\Measurement\ChartMeasurementService;
use Continuum\Service\Measurement\MeasurementService;
use Continuum\Service\MoodReflectionService;
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
        private readonly UpcomingEventService $upcomingEventService,
        private readonly HolidayService $holidayService,
        private readonly MeasurementService $measurementService,
        private readonly ChartMeasurementService $chartMeasurementService,
        private readonly WorkoutService $workoutService,
        private readonly MoodReflectionService $moodReflectionService,
        private readonly ChartMoodReflectionService $chartMoodReflectionService,
    ) {}

    #[Route(path: '/', name: 'app_homepage')]
    public function __invoke(
        #[CurrentUser] User $user,
        #[MapQueryParameter('measurement', options: ['min_range' => 1])] int $measurementMonths = 2,
        #[MapQueryParameter('workout', options: ['min_range' => 1])] int $workoutMonths = 2,
        #[MapQueryParameter('mood', options: ['min_range' => 1])] int $moodReflectionMonths = 3,
    ): Response {
        $date = new DateTimeImmutable('now', $user->getTimezone())->setTime(0, 0);

        $progress = $this->calendarProgressService->getProgress($user);

        $upcomingEvents = $this->upcomingEventService->getClosestEvents($user);
        $todayEvents = $this->holidayService->getTodayHolidays($user);

        $measurementDays = 30 * $measurementMonths;
        $prevMeasurementDate = $date->modify(sprintf('-%d days', $measurementDays));
        $measurements = $this->measurementService->getByRange($user, $prevMeasurementDate, $date);
        $chartMeasurements = $this->chartMeasurementService->getChartMeasurements(
            $user,
            $prevMeasurementDate,
            $measurements
        );

        $workoutDays = round((30 * $workoutMonths) / 7) * 7 + (int) $date->format('N');
        $workoutPrevDate = $date->modify(sprintf('-%d days', $workoutDays));
        $workouts = $this->workoutService->getByRange($user, $workoutPrevDate, $date);

        $moodReflectionDays = 30 * $moodReflectionMonths;
        $prevMoodReflectionDate = $date->modify(sprintf('-%d days', $moodReflectionDays));
        $moodReflections = $this->moodReflectionService->getPreviousDays($moodReflectionDays);
        $chartMoodReflections = $this->chartMoodReflectionService->getChartMoodReflections(
            $prevMoodReflectionDate,
            $moodReflections
        );

        return $this->render('default/homepage.html.twig', [
            'progress' => $progress,
            'upcomingEvents' => $upcomingEvents,
            'todayEvents' => $todayEvents,
            'measurementDays' => $measurementDays,
            'chartMeasurements' => $chartMeasurements,
            'workoutDays' => $workoutDays,
            'workouts' => $workouts,
            'moodReflectionDays' => $moodReflectionDays,
            'chartMoodReflections' => $chartMoodReflections,
        ]);
    }
}
