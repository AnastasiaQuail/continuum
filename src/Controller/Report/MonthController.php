<?php

declare(strict_types=1);

namespace Continuum\Controller\Report;

use Continuum\Entity\User;
use Continuum\Security\Authorization\Voter\ReportVoter;
use Continuum\Service\Calendar\CalendarEventService;
use Continuum\Service\Calendar\CalendarProgressService;
use Continuum\Service\CoupleService;
use Continuum\Service\Measurement\ChartMeasurementService;
use Continuum\Service\Measurement\MeasurementService;
use Continuum\Service\MoodReflectionService;
use Continuum\Service\WeeklyReflectionService;
use Continuum\Service\Workout\WorkoutReportService;
use Continuum\Service\Workout\WorkoutService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class MonthController extends AbstractController
{
    public function __construct(
        private readonly CalendarProgressService $calendarProgressService,
        private readonly CoupleService $coupleService,
        private readonly CalendarEventService $calendarEventService,
        private readonly MoodReflectionService $moodReflectionService,
        private readonly WeeklyReflectionService $weeklyReflectionService,
        private readonly MeasurementService $measurementService,
        private readonly ChartMeasurementService $chartMeasurementService,
        private readonly WorkoutService $workoutService,
        private readonly WorkoutReportService $workoutReportService,
    ) {}

    #[Route(path: '/reports', name: 'app_report_month', methods: ['GET'])]
    public function __invoke(#[CurrentUser] User $user, #[MapQueryParameter] ?string $month = null): Response
    {
        $startDate = new DateTimeImmutable($month ?? '-1 month', $user->timezone)
            ->modify('first day of this month')->setTime(0, 0);
        $endDate = $startDate->modify('first day of next month');

        if (!$this->isGranted(ReportVoter::MONTH_VIEW, $startDate)) {
            throw $this->createAccessDeniedException();
        }

        /** @var int $diff */
        $diff = $startDate->diff($endDate)->days;

        $progress = $this->calendarProgressService->getReportProgress($user, $startDate, $endDate);

        $coupleTogether = $this->coupleService->getTogether($user, $endDate);
        $coupleProgressDays = $coupleTogether->getDays($diff);

        $calendarEvents = $this->calendarEventService->getCountMapBetweenDates($user, $startDate);

        $moodReflections = $this->moodReflectionService->getByMonth($startDate);
        $weeklyReflections = array_filter($this->weeklyReflectionService->getByMonth($startDate));

        $measurements = $this->measurementService->getByMonth($user, $startDate);
        $chartMeasurements = $this->chartMeasurementService->getChartMeasurements($user, $startDate, $measurements);
        $offsetMeasurement = $this->chartMeasurementService->getOffsetMeasurement($chartMeasurements);

        $workouts = $this->workoutService->getByRange($user, $startDate, $endDate->modify('-1 second'));
        $workoutReport = $this->workoutReportService->getReport($workouts);

        return $this->render('report/month.html.twig', [
            'month' => $startDate,
            'progress' => $progress,
            'couple_together' => $coupleTogether,
            'couple_progress_days' => $coupleProgressDays,
            'calendar_events' => $calendarEvents,
            'mood_reflections' => $moodReflections,
            'weekly_reflections' => $weeklyReflections,
            'offset_measurement' => $offsetMeasurement,
            'workout_report' => $workoutReport,
        ]);
    }
}
