<?php

declare(strict_types=1);

namespace Continuum\Controller\Admin\User;

use Continuum\Dto\Request\Admin\User\EditUser;
use Continuum\Entity\User;
use Continuum\Form\Admin\EditUserType;
use Continuum\Security\Authorization\Voter\Admin\UserVoter;
use Continuum\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EditController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    #[Route(path: '/admin/users/{id}', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    #[IsGranted(UserVoter::EDIT, 'user')]
    public function __invoke(Request $request, User $user): Response
    {
        $form = $this->createForm(EditUserType::class, options: ['user' => $user]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditUser $dto */
            $dto = $form->getData();
            $user = $this->userService->update($user, $dto);

            $this->addFlash('success', sprintf('The %s user was updated.', $user->getUserIdentifier()));

            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
