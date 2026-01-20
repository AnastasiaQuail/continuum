<?php

declare(strict_types=1);

namespace Continuum\Controller\Health\BodyMeasurements;

use Continuum\Entity\User;
use Continuum\Security\Attribute\IsFutureMonthGranted;
use Continuum\Security\Authorization\Voter\BodyMeasurementVoter;
use Continuum\Service\BodyMeasurementService;
use Continuum\Service\ChartMeasurementService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class MonthController extends AbstractController
{
    public function __construct(
        private readonly BodyMeasurementService $bodyMeasurementService,
        private readonly ChartMeasurementService $chartMeasurementService,
    ) {}

    #[Route(path: '/health/measurements/{month}', name: 'app_health_measurements', methods: ['GET'])]
    #[IsFutureMonthGranted('month')]
    #[IsGranted(BodyMeasurementVoter::VIEW)]
    public function __invoke(#[CurrentUser] User $user, ?DateTimeImmutable $month = null): Response
    {
        $month ??= new DateTimeImmutable('first day of this month', $user->getTimezone())->setTime(0, 0);

        $measurements = $this->bodyMeasurementService->getByMonth($user, $month);
        $chartMeasurements = $this->chartMeasurementService->getChartMeasurements($user, $month, $measurements);

        return $this->render('health/measurements/index.html.twig', [
            'month' => $month,
            'measurements' => $measurements,
            'chartMeasurements' => $chartMeasurements,
        ]);
    }
}
