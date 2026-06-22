<?php

declare(strict_types=1);

namespace Continuum\Controller\Measurement;

use Continuum\Dto\Request\Measurement\EditMeasurement;
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

final class CreateController extends AbstractController
{
    public function __construct(
        private readonly MeasurementService $measurementService,
    ) {}

    #[Route(path: '/measurements/create', name: 'app_measurement_create', methods: ['GET', 'POST'])]
    #[IsGranted(MeasurementVoter::CREATE)]
    public function __invoke(#[CurrentUser] User $user, Request $request): Response
    {
        $form = $this->createForm(EditMeasurementType::class, options: [
            'lastMeasurement' => $this->measurementService->getLastMeasurement(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditMeasurement $dto */
            $dto = $form->getData();
            $measurement = $this->measurementService->save($user, $dto);
            $datetime = $measurement->datetime->setTimezone($user->timezone);

            $this->addFlash('success', sprintf('The "%s" measurement was created', $datetime->format('j F H:i')));

            return $this->redirectToRoute('app_measurements', ['month' => $datetime->format('Y-m')]);
        }

        return $this->render('measurement/edit.html.twig', [
            'form' => $form,
            'measurement' => null,
        ]);
    }
}
