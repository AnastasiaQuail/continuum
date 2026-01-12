<?php

declare(strict_types=1);

namespace Continuum\Controller\Reflection\Mood;

use Continuum\Entity\User;
use Continuum\Form\EditReflectionMoodType;
use Continuum\Service\ReflectionMoodService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class DayController extends AbstractController
{
    public function __construct(
        private readonly ReflectionMoodService $reflectionMoodService,
    ) {}

    #[Route(path: '/reflection/mood/days/{day}', name: 'app_reflection_mood_day', methods: ['GET', 'POST'])]
    public function __invoke(Request $request, #[CurrentUser] User $user, DateTimeImmutable $day): Response
    {
        $mood = $this->reflectionMoodService->findMoodByDay($day);

        $form = $this->createForm(EditReflectionMoodType::class, options: ['mood' => $mood]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mood = $this->reflectionMoodService->save($day, $mood, $form->getData());

            $this->addFlash('success', sprintf('The mood for %s was updated.', $mood->getDate()->format('j F')));

            return $this->redirectToRoute('app_reflection_mood', ['month' => $day->format('Y-m')]);
        }

        return $this->render('reflection/mood/day.html.twig', [
            'day' => $day,
            'form' => $form,
        ]);
    }
}
