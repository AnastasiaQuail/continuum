<?php

declare(strict_types=1);

namespace Continuum\Controller\Workout\Exercise;

use Continuum\Entity\User;
use Continuum\Security\Authorization\Voter\ExerciseVoter;
use Continuum\Service\Workout\ExerciseService;
use Continuum\Service\Workout\WorkoutProgressService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
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
    public function __invoke(
        #[CurrentUser]
        User $user,
        #[MapQueryParameter(options: ['min_range' => 4, 'max_range' => 10])]
        int $months = 4,
    ): Response {
        $exercises = $this->exerciseService->getAll();
        $exerciseCountMap = $this->exerciseService->getWorkoutExerciseCountIndexedById();
        $exerciseDays = 30 * $months;
        $exerciseProgressMap = $this->workoutProgressService->getScoreProgresses($user, $exerciseDays);

        return $this->render('workout/exercises/index.html.twig', [
            'exercises' => $exercises,
            'exercise_count_map' => $exerciseCountMap,
            'exercise_days' => $exerciseDays,
            'exercise_progress_map' => $exerciseProgressMap,
        ]);
    }
}
