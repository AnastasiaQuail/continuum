<?php

declare(strict_types=1);

namespace Continuum\Controller\Measurement;

use Continuum\Dto\Request\Measurement\EditMeasurement;
use Continuum\Entity\BodyMeasurement;
use Continuum\Entity\User;
use Continuum\Form\EditMeasurementType;
use Continuum\Security\Authorization\Voter\MeasurementVoter;
use Continuum\Service\Measurement\MeasurementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EditController extends AbstractController
{
    public function __construct(
        private readonly MeasurementService $measurementService,
    ) {}

    #[Route(path: '/measurements/{id}', name: 'app_measurement_edit', methods: ['GET', 'POST'])]
    #[IsGranted(MeasurementVoter::EDIT, 'measurement')]
    public function __invoke(#[CurrentUser] User $user, Request $request, BodyMeasurement $measurement): Response
    {
        $form = $this->createForm(EditMeasurementType::class, null, ['measurement' => $measurement]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditMeasurement $dto */
            $dto = $form->getData();
            $measurement = $this->measurementService->save($user, $measurement, $dto);
            $datetime = $measurement->getDatetime()->setTimezone($user->getTimezone());

            $this->addFlash('success', sprintf('The "%s" measurement was updated', $datetime->format('j F H:i')));

            return $this->redirectToRoute('app_measurements', ['month' => $datetime->format('Y-m')]);
        }

        return $this->render('measurement/edit.html.twig', [
            'form' => $form,
            'measurement' => $measurement,
        ]);
    }
}
