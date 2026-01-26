<?php

declare(strict_types=1);

namespace Continuum\Controller\Workout;

use Continuum\Service\Workout\WorkoutService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WorkoutCreateController extends AbstractController
{
    public function __construct(
        private readonly WorkoutService $workoutService,
    ) {}

    #[Route(path: '/workouts', name: 'app_workout_create', methods: ['POST'])]
    public function __invoke(): Response
    {
        $workout = $this->workoutService->create();

        return $this->redirectToRoute('app_workouts_view', ['id' => $workout->getId()]);
    }
}
