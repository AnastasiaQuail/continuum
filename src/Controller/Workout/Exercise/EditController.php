<?php

declare(strict_types=1);

namespace Continuum\Controller\Workout\Exercise;

use Continuum\Entity\Exercise;
use Continuum\Form\EditExerciseType;
use Continuum\Service\Workout\ExerciseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EditController extends AbstractController
{
    public function __construct(
        private readonly ExerciseService $exerciseService,
    ) {}

    #[Route(path: '/exercises/{id}', name: 'app_exercise_edit', methods: ['GET', 'POST'])]
    public function __invoke(Request $request, Exercise $exercise): Response
    {
        $form = $this->createForm(EditExerciseType::class, options: ['exercise' => $exercise]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $exercise = $this->exerciseService->update($exercise, $form->getData());

            $this->addFlash('success', sprintf('The "%s" exercise was updated.', $exercise->getName()));

            return $this->redirectToRoute('app_exercises');
        }

        return $this->render('workout/exercises/edit.html.twig', [
            'exercise' => $exercise,
            'form' => $form,
        ]);
    }
}
