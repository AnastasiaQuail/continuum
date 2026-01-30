<?php

declare(strict_types=1);

namespace Continuum\Controller\Admin\User;

use Continuum\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    #[Route(path: '/admin/users', name: 'app_admin_users')]
    public function __invoke(): Response
    {
        $users = $this->userService->getAll();

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }
}
