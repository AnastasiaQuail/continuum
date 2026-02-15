<?php

declare(strict_types=1);

namespace Continuum\Controller\User;

use Continuum\Dto\Request\User\EditLocation;
use Continuum\Entity\User;
use Continuum\Form\EditLocationType;
use Continuum\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class LocationEditController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    #[Route(path: '/profile/location', name: 'app_profile_location', methods: ['GET', 'POST'])]
    public function __invoke(#[CurrentUser] User $user, Request $request): Response
    {
        $form = $this->createForm(EditLocationType::class, null, ['location' => $user->location]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditLocation $dto */
            $dto = $form->getData();
            $this->userService->updateLocation($user, $dto);

            $this->addFlash('success', 'Location updated.');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('user/location.html.twig', [
            'form' => $form,
        ]);
    }
}
