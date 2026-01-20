<?php

declare(strict_types=1);

namespace Continuum\Controller\Reflection\Mood;

use Continuum\Entity\User;
use Continuum\Security\Attribute\IsFutureMonthGranted;
use Continuum\Security\Authorization\Voter\MoodReflectionVoter;
use Continuum\Service\MoodReflectionService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class MoodController extends AbstractController
{
    public function __construct(
        private readonly MoodReflectionService $moodReflectionService,
    ) {}

    #[Route(path: '/reflection/mood/{month}', name: 'app_mood_reflection', methods: ['GET'])]
    #[IsFutureMonthGranted('month')]
    #[IsGranted(MoodReflectionVoter::VIEW)]
    public function __invoke(#[CurrentUser] User $user, ?DateTimeImmutable $month = null): Response
    {
        $month ??= new DateTimeImmutable('first day of this month', $user->getTimezone())->setTime(0, 0);

        $lastMoods = $this->moodReflectionService->getPreviousDays();
        $moods = $this->moodReflectionService->getByMonth($month);

        return $this->render('reflection/mood/index.html.twig', [
            'month' => $month,
            'lastMoods' => $lastMoods,
            'moods' => $moods,
        ]);
    }
}
