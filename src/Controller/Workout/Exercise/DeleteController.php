<?php

declare(strict_types=1);

namespace Continuum\Controller\Workout\Exercise;

use Continuum\Entity\Exercise;
use Continuum\Security\Authorization\Voter\ExerciseVoter;
use Continuum\Service\Workout\ExerciseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DeleteController extends AbstractController
{
    public function __construct(
        private readonly ExerciseService $exerciseService,
    ) {}

    #[Route(path: '/exercises/{id}', name: 'app_exercise_delete', methods: ['DELETE'])]
    #[IsGranted(ExerciseVoter::DELETE, 'exercise')]
    public function __invoke(Exercise $exercise): RedirectResponse
    {
        $this->exerciseService->delete($exercise);

        $this->addFlash('success', sprintf('Exercise "%s" has been deleted.', $exercise->name));

        return $this->redirectToRoute('app_exercises');
    }
}
