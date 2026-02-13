<?php

declare(strict_types=1);

namespace Continuum\Controller\Measurement;

use Continuum\Entity\User;
use Continuum\Security\Authorization\Voter\MeasurementVoter;
use Continuum\Service\Measurement\ChartMeasurementService;
use Continuum\Service\Measurement\MeasurementService;
use Continuum\Service\RequestValidator;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class IndexController extends AbstractController
{
    public function __construct(
        private readonly RequestValidator $requestValidator,
        private readonly MeasurementService $measurementService,
        private readonly ChartMeasurementService $chartMeasurementService,
    ) {}

    #[Route(path: '/measurements', name: 'app_measurements', methods: ['GET'])]
    public function __invoke(
        #[CurrentUser]
        User $user,
        #[MapQueryParameter('month')]
        ?string $date = null,
    ): Response {
        $month = new DateTimeImmutable($date ?? 'first day of this month', $user->getTimezone())->setTime(0, 0);

        if (null !== $error = $this->requestValidator->validateExistenceMonth($month, $user->getTimezone())) {
            throw new BadRequestHttpException($error);
        }

        if (!$this->isGranted(MeasurementVoter::VIEW, $month)) {
            throw $this->createAccessDeniedException();
        }

        $measurements = $this->measurementService->getByMonth($user, $month);
        $chartMeasurements = $this->chartMeasurementService->getChartMeasurements($user, $month, $measurements);

        return $this->render('measurement/index.html.twig', [
            'month' => $month,
            'measurements' => $measurements,
            'chartMeasurements' => $chartMeasurements,
        ]);
    }
}
