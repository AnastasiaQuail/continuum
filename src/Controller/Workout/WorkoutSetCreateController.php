<?php

declare(strict_types=1);

namespace Continuum\Controller\Workout;

use Continuum\Dto\Request\Workout\NewWorkoutSet;
use Continuum\Entity\WorkoutExercise;
use Continuum\Service\Workout\WorkoutSetService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class WorkoutSetCreateController extends AbstractController
{
    public function __construct(
        private readonly WorkoutSetService $workoutSetService,
    ) {}

    #[Route(path: '/workouts/exercises/{id}', name: 'app_workout_set_create', methods: ['POST'])]
    public function __invoke(WorkoutExercise $workoutExercise, #[MapRequestPayload] NewWorkoutSet $dto): Response
    {
        $workoutSet = $this->workoutSetService->create($workoutExercise, $dto);

        return $this->redirectToRoute('app_workouts_view', [
            'id' => $workoutSet->getWorkoutExercise()->getWorkout()->getId(),
        ]);
    }
}
