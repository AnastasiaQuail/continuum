<?php

declare(strict_types=1);

namespace Continuum\Controller\Health\BodyMeasurements;

use Continuum\Dto\Response\Health\LastBodyMeasurement;
use Continuum\Entity\BodyMeasurement;
use Continuum\Entity\User;
use Continuum\Form\EditBodyMeasurementType;
use Continuum\Service\BodyMeasurementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class EditController extends AbstractController
{
    public function __construct(
        private readonly BodyMeasurementService $bodyMeasurementService,
    ) {}

    #[Route(path: '/health/measurements/body/{id?}', name: 'app_health_measurement_edit', methods: ['GET', 'POST'])]
    public function __invoke(
        #[CurrentUser] User $user,
        Request $request,
        ?BodyMeasurement $measurement = null
    ): Response {
        $lastMeasurement = new LastBodyMeasurement(20, 150, 100.0);

        $form = $this->createForm(EditBodyMeasurementType::class, null, [
            'lastMeasurement' => $lastMeasurement,
            'measurement' => $measurement,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $measurement = $this->bodyMeasurementService->save($user, $measurement, $form->getData());

            $this->addFlash(
                'success',
                sprintf('The "%s" measurement was saved', $measurement->getDatetime()->format('Y-m-d H:i:s')),
            );

            return $this->redirectToRoute('app_health_measurements');
        }

        return $this->render('health/measurements/edit.html.twig', [
            'form' => $form,
            'measurement' => $measurement,
        ]);
    }
}
