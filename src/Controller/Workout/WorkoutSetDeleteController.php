<?php

declare(strict_types=1);

namespace Continuum\Controller\Workout;

use Continuum\Entity\WorkoutSet;
use Continuum\Security\Authorization\Voter\WorkoutVoter;
use Continuum\Service\Workout\WorkoutSetService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class WorkoutSetDeleteController extends AbstractController
{
    public function __construct(
        private readonly WorkoutSetService $workoutSetService,
    ) {}

    #[Route(path: '/workouts/exercises/sets/{id}', name: 'app_workout_set_delete', methods: ['DELETE'])]
    #[IsGranted(WorkoutVoter::SET_DELETE, 'workoutSet')]
    public function __invoke(WorkoutSet $workoutSet): Response
    {
        $this->workoutSetService->delete($workoutSet);

        return $this->redirectToRoute('app_workouts_view', [
            'id' => $workoutSet->getWorkoutExercise()->getWorkout()->getId(),
        ]);
    }
}
