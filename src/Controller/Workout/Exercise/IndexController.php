<?php

declare(strict_types=1);

namespace Continuum\Controller\Workout\Exercise;

use Continuum\Entity\User;
use Continuum\Security\Authorization\Voter\ExerciseVoter;
use Continuum\Service\Workout\ExerciseService;
use Continuum\Service\Workout\WorkoutProgressService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class IndexController extends AbstractController
{
    public function __construct(
        private readonly ExerciseService $exerciseService,
        private readonly WorkoutProgressService $workoutProgressService,
    ) {}

    #[Route(path: '/exercises', name: 'app_exercises', methods: ['GET'])]
    #[IsGranted(ExerciseVoter::VIEW)]
    public function __invoke(#[CurrentUser] User $user): Response
    {
        $exercises = $this->exerciseService->getAll();
        $exerciseCountMap = $this->exerciseService->getWorkoutExerciseCountIndexedById();
        $exerciseProgressMap = $this->workoutProgressService->getScoreProgresses($user);

        return $this->render('workout/exercises/index.html.twig', [
            'exercises' => $exercises,
            'exercise_count_map' => $exerciseCountMap,
            'exercise_progress_map' => $exerciseProgressMap,
        ]);
    }
}
