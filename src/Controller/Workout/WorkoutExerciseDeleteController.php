<?php

declare(strict_types=1);

namespace Continuum\Controller\Workout;

use Continuum\Entity\WorkoutExercise;
use Continuum\Service\Workout\WorkoutExerciseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WorkoutExerciseDeleteController extends AbstractController
{
    public function __construct(
        private readonly WorkoutExerciseService $workoutExerciseService,
    ) {}

    #[Route(path: '/workouts/exercises/{id}', name: 'app_workout_exercise_delete', methods: ['DELETE'])]
    public function __invoke(WorkoutExercise $workoutExercise): Response
    {
        $this->workoutExerciseService->delete($workoutExercise);

        return $this->redirectToRoute('app_workouts_view', ['id' => $workoutExercise->getWorkout()->getId()]);
    }
}
