<?php

declare(strict_types=1);

namespace Continuum\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route(path: '/profile', name: 'app_profile', methods: ['GET'])]
    public function __invoke(): Response
    {
        return  $this->render('user/profile.html.twig');
    }
}
