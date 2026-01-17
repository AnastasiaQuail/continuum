<?php

declare(strict_types=1);

namespace Continuum\Controller\Reflection\Mood;

use Continuum\Entity\User;
use Continuum\Form\EditMoodReflectionType;
use Continuum\Service\MoodReflectionService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class EditController extends AbstractController
{
    public function __construct(
        private readonly MoodReflectionService $moodReflectionService,
    ) {}

    #[Route(path: '/reflection/mood/days/{day}', name: 'app_mood_reflection_edit', methods: ['GET', 'POST'])]
    public function __invoke(Request $request, #[CurrentUser] User $user, DateTimeImmutable $day): Response
    {
        $moodReflection = $this->moodReflectionService->findMoodByDay($day);

        $form = $this->createForm(EditMoodReflectionType::class, options: ['moodReflection' => $moodReflection]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $moodReflection = $this->moodReflectionService->save($day, $moodReflection, $form->getData());

            $this->addFlash(
                'success',
                sprintf('The mood for %s was saved.', $moodReflection->getDate()->format('j F'))
            );

            return $this->redirectToRoute('app_mood_reflection', ['month' => $day->format('Y-m')]);
        }

        return $this->render('reflection/mood/edit.html.twig', [
            'day' => $day,
            'form' => $form,
        ]);
    }
}
