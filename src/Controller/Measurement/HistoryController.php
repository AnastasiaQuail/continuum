<?php

declare(strict_types=1);

namespace Continuum\Controller\Measurement;

use Continuum\Entity\BodyMeasurement;
use Continuum\Entity\User;
use Continuum\Security\Authorization\Voter\MeasurementVoter;
use Continuum\Service\Measurement\MeasurementService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class HistoryController extends AbstractController
{
    public function __construct(
        private readonly MeasurementService $measurementService,
    ) {}

    #[Route(path: '/measurements/history', name: 'app_measurements_history', methods: ['GET'])]
    #[IsGranted(MeasurementVoter::HISTORY)]
    public function __invoke(
        #[CurrentUser]
        User $user,
        #[MapQueryParameter(options: ['min_range' => 4, 'max_range' => 10])]
        int $months = 4,
    ): Response {
        $day = new DateTimeImmutable('now', $user->timezone)->setTime(0, 0);

        $measurementDays = 30 * $months;
        $prevDay = $day->modify(sprintf('-%s days', $measurementDays));

        $measurements = $this->measurementService->getByRange($user, $prevDay, $day);

        return $this->render('measurement/history.html.twig', [
            'measurements' => $measurements,
            'measurement_days' => $measurementDays,
            'reversed_measurement_names' => BodyMeasurement::getProgressReversedMeasurementNames(),
        ]);
    }
}
