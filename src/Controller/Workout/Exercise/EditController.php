<?php

declare(strict_types=1);

namespace Continuum\Controller\Workout\Exercise;

use Continuum\Dto\Request\Workout\EditExercise;
use Continuum\Entity\Exercise;
use Continuum\Form\EditExerciseType;
use Continuum\Security\Authorization\Voter\ExerciseVoter;
use Continuum\Service\Workout\ExerciseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EditController extends AbstractController
{
    public function __construct(
        private readonly ExerciseService $exerciseService,
    ) {}

    #[Route(
        path: '/exercises/{id}',
        name: 'app_exercise_edit',
        requirements: ['id' => Requirement::UUID],
        methods: ['GET', 'POST'],
    )]
    #[IsGranted(ExerciseVoter::EDIT, 'exercise')]
    public function __invoke(Request $request, Exercise $exercise): Response
    {
        $form = $this->createForm(EditExerciseType::class, options: ['exercise' => $exercise]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditExercise $dto */
            $dto = $form->getData();
            $exercise = $this->exerciseService->update($exercise, $dto);

            $this->addFlash('success', sprintf('The "%s" exercise was updated.', $exercise->name));

            return $this->redirectToRoute('app_exercises');
        }

        return $this->render('workout/exercises/edit.html.twig', [
            'exercise' => $exercise,
            'form' => $form,
        ]);
    }
}
