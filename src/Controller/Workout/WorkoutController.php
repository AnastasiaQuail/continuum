<?php

declare(strict_types=1);

namespace Continuum\Controller\Workout;

use Continuum\Service\Workout\ExerciseService;
use Continuum\Service\Workout\WorkoutService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

final class WorkoutController extends AbstractController
{
    public function __construct(
        private readonly WorkoutService $workoutService,
        private readonly ExerciseService $exerciseService,
    ) {}

    #[Route(path: '/workouts/{id}', name: 'app_workouts_view', methods: ['GET'])]
    public function __invoke(Uuid $id): Response
    {
        $workout = $this->workoutService->getById($id);
        $exercises = $this->exerciseService->getAll();

        return $this->render('workout/view.html.twig', [
            'workout' => $workout,
            'exercises' => $exercises,
        ]);
    }
}
