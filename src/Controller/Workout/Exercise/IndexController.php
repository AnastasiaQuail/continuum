<?php

declare(strict_types=1);

namespace Continuum\Controller\Workout\Exercise;

use Continuum\Service\Workout\ExerciseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexController extends AbstractController
{
    public function __construct(
        private readonly ExerciseService $exerciseService,
    ) {}

    #[Route(path: '/exercises', name: 'app_exercises', methods: ['GET'])]
    public function __invoke(): Response {
        $exercises = $this->exerciseService->getAll();
        $exerciseCountMap = $this->exerciseService->getWorkoutExerciseCountIndexedById();

        return $this->render('workout/exercises/index.html.twig', [
            'exercises' => $exercises,
            'exerciseCountMap' => $exerciseCountMap,
        ]);
    }
}
