<?php

declare(strict_types=1);

namespace Continuum\Controller\Workout;

use Continuum\Entity\WorkoutSet;
use Continuum\Service\Workout\WorkoutSetService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WorkoutSetDeleteController extends AbstractController
{
    public function __construct(
        private readonly WorkoutSetService $workoutSetService,
    ) {}

    #[Route(path: '/workouts/sets/{id}', name: 'app_workout_set_delete', methods: ['DELETE'])]
    public function __invoke(WorkoutSet $workoutSet): Response
    {
        $this->workoutSetService->delete($workoutSet);

        return $this->redirectToRoute('app_workouts_view', [
            'id' => $workoutSet->getWorkoutExercise()->getWorkout()->getId(),
        ]);
    }
}
