<?php

declare(strict_types=1);

namespace Continuum\Controller\Report;

use Continuum\Entity\User;
use Continuum\Service\Calendar\CalendarEventService;
use Continuum\Service\Calendar\CalendarProgressService;
use Continuum\Service\CoupleService;
use Continuum\Service\MoodReflectionService;
use Continuum\Service\WeeklyReflectionService;
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
    ) {}

    #[Route('/reports/month', name: 'app_report_month', methods: ['GET'])]
    public function __invoke(#[CurrentUser] User $user, #[MapQueryParameter] ?string $month = null): Response
    {
        $endDate = new DateTimeImmutable($month ?? '-1 month', $user->timezone)
            ->modify('last day of this month')->setTime(23, 59, 59);
        $startDate = $endDate->modify('last day of previous month');

        /** @var int $diff */
        $diff = $startDate->diff($endDate)->days;

        $progress = $this->calendarProgressService->getReportProgress($user, $startDate, $endDate);

        $coupleTogether = $this->coupleService->getTogether($user, $endDate);
        $coupleProgressDays = $coupleTogether->getDays($diff);

        $calendarEvents = $this->calendarEventService->getCountMapBetweenDates($user, $endDate);

        $moodReflections = $this->moodReflectionService->getByMonth($endDate);
        $weeklyReflections = array_filter($this->weeklyReflectionService->getByMonth($endDate));

        return $this->render('report/month.html.twig', [
            'month' => $endDate,
            'progress' => $progress,
            'couple_together' => $coupleTogether,
            'couple_progress_days' => $coupleProgressDays,
            'calendar_events' => $calendarEvents,
            'mood_reflections' => $moodReflections,
            'weekly_reflections' => $weeklyReflections,
        ]);
    }
}
