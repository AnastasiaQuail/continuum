<?php

declare(strict_types=1);

namespace Continuum\Controller\Workout;

use Continuum\Entity\Workout;
use Continuum\Security\Authorization\Voter\WorkoutVoter;
use Continuum\Service\Workout\WorkoutService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class WorkoutDeleteController extends AbstractController
{
    public function __construct(
        private readonly WorkoutService $workoutService,
    ) {}

    #[Route(path: '/workouts/{id}', name: 'app_workout_delete', methods: ['DELETE'])]
    #[IsGranted(WorkoutVoter::DELETE, 'workout')]
    public function __invoke(Workout $workout): RedirectResponse
    {
        $this->workoutService->delete($workout);

        return $this->redirectToRoute('app_workouts');
    }
}
