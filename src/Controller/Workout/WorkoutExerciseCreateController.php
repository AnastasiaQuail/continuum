<?php

declare(strict_types=1);

namespace Continuum\Controller\Workout;

use Continuum\Entity\Workout;
use Continuum\Security\Authorization\Voter\WorkoutVoter;
use Continuum\Service\Workout\ExerciseService;
use Continuum\Service\Workout\WorkoutExerciseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

final class WorkoutExerciseCreateController extends AbstractController
{
    public function __construct(
        private readonly ExerciseService $exerciseService,
        private readonly WorkoutExerciseService $workoutExerciseService,
    ) {}

    #[Route(path: '/workouts/{id}/exercises', name: 'app_workout_exercise_create', methods: ['POST'])]
    #[IsGranted(WorkoutVoter::EXERCISE_CREATE, 'workout')]
    public function __invoke(Request $request, Workout $workout): RedirectResponse
    {
        $exerciseId = Uuid::fromString($request->request->getString('exercise'));
        $exercise = $this->exerciseService->getById($exerciseId);

        $workoutExercise = $this->workoutExerciseService->create($workout, $exercise);

        return $this->redirectToRoute('app_workouts_view', ['id' => $workoutExercise->workout->id]);
    }
}
