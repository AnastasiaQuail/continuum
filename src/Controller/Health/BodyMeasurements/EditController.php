<?php

declare(strict_types=1);

namespace Continuum\Controller\Health\BodyMeasurements;

use Continuum\Entity\BodyMeasurement;
use Continuum\Entity\User;
use Continuum\Form\EditBodyMeasurementType;
use Continuum\Security\Authorization\Voter\BodyMeasurementVoter;
use Continuum\Service\BodyMeasurementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EditController extends AbstractController
{
    public function __construct(
        private readonly BodyMeasurementService $bodyMeasurementService,
    ) {}

    #[Route(path: '/health/measurements/body/{id?}', name: 'app_health_measurement_edit', methods: ['GET', 'POST'])]
    #[IsGranted(BodyMeasurementVoter::EDIT, 'measurement')]
    public function __invoke(
        #[CurrentUser] User $user,
        Request $request,
        ?BodyMeasurement $measurement = null
    ): Response {
        $form = $this->createForm(EditBodyMeasurementType::class, null, [
            'lastMeasurement' => $measurement === null ? $this->bodyMeasurementService->getLastMeasurement() : null,
            'measurement' => $measurement,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $measurement = $this->bodyMeasurementService->save($user, $measurement, $form->getData());
            $datetime = $measurement->getDatetime()->setTimezone($user->getTimezone());

            $this->addFlash('success', sprintf('The "%s" measurement was saved', $datetime->format('j F H:i')));

            return $this->redirectToRoute('app_health_measurements', ['month' => $datetime->format('Y-m')]);
        }

        return $this->render('health/measurements/edit.html.twig', [
            'form' => $form,
            'measurement' => $measurement,
        ]);
    }
}
