<?php

declare(strict_types=1);

namespace Continuum\Controller\Measurement;

use Continuum\Entity\User;
use Continuum\Security\Attribute\IsFutureMonthGranted;
use Continuum\Security\Authorization\Voter\MeasurementVoter;
use Continuum\Service\Measurement\ChartMeasurementService;
use Continuum\Service\Measurement\MeasurementService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class IndexController extends AbstractController
{
    public function __construct(
        private readonly MeasurementService $measurementService,
        private readonly ChartMeasurementService $chartMeasurementService,
    ) {}

    #[Route(path: '/measurements/{month}', name: 'app_measurements', methods: ['GET'])]
    #[IsFutureMonthGranted('month')]
    #[IsGranted(MeasurementVoter::VIEW)]
    public function __invoke(#[CurrentUser] User $user, ?DateTimeImmutable $month = null): Response
    {
        $month ??= new DateTimeImmutable('first day of this month', $user->getTimezone())->setTime(0, 0);

        $measurements = $this->measurementService->getByMonth($user, $month);
        $chartMeasurements = $this->chartMeasurementService->getChartMeasurements($user, $month, $measurements);

        return $this->render('measurement/index.html.twig', [
            'month' => $month,
            'measurements' => $measurements,
            'chartMeasurements' => $chartMeasurements,
        ]);
    }
}
