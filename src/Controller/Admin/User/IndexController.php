<?php

declare(strict_types=1);

namespace Continuum\Controller\Admin\User;

use Continuum\Security\Authorization\Voter\Admin\UserVoter;
use Continuum\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class IndexController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    #[Route(path: '/admin/users', name: 'app_admin_users', methods: ['GET'])]
    #[IsGranted(UserVoter::VIEW)]
    public function __invoke(): Response
    {
        $users = $this->userService->getAll();

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }
}
