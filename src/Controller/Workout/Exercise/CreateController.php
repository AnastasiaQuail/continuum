<?php

declare(strict_types=1);

namespace Continuum\Controller\Workout\Exercise;

use Continuum\Form\EditExerciseType;
use Continuum\Security\Authorization\Voter\ExerciseVoter;
use Continuum\Service\Workout\ExerciseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CreateController extends AbstractController
{
    public function __construct(
        private readonly ExerciseService $exerciseService,
    ) {}

    #[Route(path: '/exercises/create', name: 'app_exercise_create', methods: ['GET', 'POST'])]
    #[IsGranted(ExerciseVoter::CREATE)]
    public function __invoke(Request $request): Response
    {
        $form = $this->createForm(EditExerciseType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $exercise = $this->exerciseService->create($form->getData());

            $this->addFlash('success', sprintf('The "%s" exercise was created.', $exercise->getName()));

            return $this->redirectToRoute('app_exercises');
        }

        return $this->render('workout/exercises/edit.html.twig', [
            'exercise' => null,
            'form' => $form,
        ]);
    }
}
