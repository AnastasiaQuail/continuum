<?php

declare(strict_types=1);

namespace Continuum\Controller\Workout;

use Continuum\Dto\Request\Workout\NewWorkoutSet;
use Continuum\Entity\WorkoutExercise;
use Continuum\Security\Authorization\Voter\WorkoutVoter;
use Continuum\Service\Workout\WorkoutSetService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class WorkoutSetCreateController extends AbstractController
{
    public function __construct(
        private readonly WorkoutSetService $workoutSetService,
    ) {}

    #[Route(path: '/workouts/exercises/{id}/sets', name: 'app_workout_set_create', methods: ['POST'])]
    #[IsGranted(WorkoutVoter::SET_CREATE, 'workoutExercise')]
    public function __invoke(
        WorkoutExercise $workoutExercise,
        #[MapRequestPayload] NewWorkoutSet $dto
    ): RedirectResponse {
        $workoutSet = $this->workoutSetService->create($workoutExercise, $dto);

        return $this->redirectToRoute('app_workouts_view', [
            'id' => $workoutSet->workoutExercise->workout->id,
        ]);
    }
}
