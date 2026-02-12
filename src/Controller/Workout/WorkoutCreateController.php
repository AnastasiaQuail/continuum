<?php

declare(strict_types=1);

namespace Continuum\Controller\Workout;

use Continuum\Security\Authorization\Voter\WorkoutVoter;
use Continuum\Service\Workout\WorkoutService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class WorkoutCreateController extends AbstractController
{
    public function __construct(
        private readonly WorkoutService $workoutService,
    ) {}

    #[Route(path: '/workouts', name: 'app_workout_create', methods: ['POST'])]
    #[IsGranted(WorkoutVoter::CREATE)]
    public function __invoke(): RedirectResponse
    {
        $workout = $this->workoutService->create();

        return $this->redirectToRoute('app_workouts_view', ['id' => $workout->getId()]);
    }
}
