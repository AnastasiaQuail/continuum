<?php

declare(strict_types=1);

namespace Continuum\Controller\Workout;

use Continuum\Entity\Workout;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WorkoutController extends AbstractController
{
    #[Route(path: '/workouts/{id}', name: 'app_workouts_view', methods: ['GET'])]
    public function __invoke(Workout $workout): Response
    {
        return $this->render('workout/view.html.twig', [
            'workout' => $workout,
        ]);
    }
}
