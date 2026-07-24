<?php

declare(strict_types=1);

namespace Continuum\Controller\Measurement;

use Continuum\Entity\BodyMeasurement;
use Continuum\Entity\User;
use Continuum\Security\Authorization\Voter\MeasurementVoter;
use Continuum\Service\Measurement\MeasurementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DeleteController extends AbstractController
{
    public function __construct(
        private readonly MeasurementService $measurementService,
    ) {}

    #[Route(
        path: '/measurements/{id}',
        name: 'app_measurement_delete',
        requirements: ['id' => Requirement::UUID],
        methods: ['DELETE'],
    )]
    #[IsGranted(MeasurementVoter::DELETE, 'measurement')]
    public function __invoke(#[CurrentUser] User $user, BodyMeasurement $measurement): RedirectResponse
    {
        $this->measurementService->delete($measurement);

        $datetime = $measurement->datetime->setTimezone($user->timezone);

        $this->addFlash('success', sprintf('Measurement "%s" has been deleted.', $datetime->format('j F, H:i')));

        return $this->redirectToRoute('app_measurements');
    }
}
