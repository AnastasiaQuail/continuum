<?php

declare(strict_types=1);

namespace Continuum\Controller;

use Continuum\Service\Calendar\CalendarProgressService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomepageController extends AbstractController
{
    public function __construct(
        private readonly CalendarProgressService $calendarProgressService,
    ) {}

    #[Route(path: '/', name: 'app_homepage')]
    public function __invoke(): Response
    {
        return $this->render('default/homepage.html.twig', [
            'calendarProgress' => $this->calendarProgressService->getCurrentProgress(),
        ]);
    }
}
