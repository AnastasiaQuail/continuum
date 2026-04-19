<?php

declare(strict_types=1);

namespace Continuum\Controller\MoodReflection;

use Continuum\Entity\User;
use Continuum\Security\Authorization\Voter\MoodReflectionVoter;
use Continuum\Service\MoodReflectionService;
use Continuum\Service\RequestValidator;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class LastUnfilledController extends AbstractController
{
    public function __construct(
        private readonly RequestValidator $requestValidator,
        private readonly MoodReflectionService $moodReflectionService,
    ) {}

    #[Route(path: '/mood-reflections/unfilled', name: 'app_mood_reflection_last_unfilled', methods: ['GET'])]
    #[IsGranted(MoodReflectionVoter::EDIT)]
    public function __invoke(#[CurrentUser] User $user, Request $request): RedirectResponse
    {
        $moodReflection = $this->moodReflectionService->findLastMood();

        if (null === $moodReflection) {
            $this->addFlash('warning', "You have no mood reflections yet. Let's create your first one!");

            return $this->redirectToRoute('app_mood_reflections');
        }

        $unfilledDay = $moodReflection->date->modify('+1 day');
        $day = new DateTimeImmutable($unfilledDay->format('Y-m-d'), $user->timezone)->setTime(0, 0);

        if (null !== $this->requestValidator->validateExistenceDay($day)) {
            $this->addFlash('success', 'Your mood reflection is up to date!');

            if (null !== $referer = $request->headers->get('referer')) {
                return $this->redirect($referer);
            }

            return $this->redirectToRoute('app_mood_reflections');
        }

        return $this->redirectToRoute('app_mood_reflection_edit', ['day' => $day->format('Y-m-d')]);
    }
}
