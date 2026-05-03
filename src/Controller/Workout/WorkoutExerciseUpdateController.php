<?php

declare(strict_types=1);

namespace Continuum\Controller\Workout;

use Continuum\Entity\WorkoutExercise;
use Continuum\Security\Authorization\Voter\WorkoutVoter;
use Continuum\Service\Workout\WorkoutExerciseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class WorkoutExerciseUpdateController extends AbstractController
{
    public function __construct(
        private readonly WorkoutExerciseService $workoutExerciseService,
    ) {}

    #[Route(path: '/workouts/exercises/{id}', name: 'app_workout_exercise_update', methods: ['PATCH'])]
    #[IsGranted(WorkoutVoter::EXERCISE_UPDATE, 'workoutExercise')]
    public function __invoke(Request $request, WorkoutExercise $workoutExercise): RedirectResponse
    {
        $description = trim($request->request->getString('description'));

        $this->workoutExerciseService->updateDescription($workoutExercise, $description);

        return $this->redirectToRoute('app_workouts_view', ['id' => $workoutExercise->workout->id]);
    }
}
