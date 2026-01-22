<?php

declare(strict_types=1);

namespace Continuum\Controller\MoodReflection;

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

final class IndexController extends AbstractController
{
    public function __construct(
        private readonly MoodReflectionService $moodReflectionService,
    ) {}

    #[Route(path: '/mood-reflections/{month}', name: 'app_mood_reflections', methods: ['GET'])]
    #[IsFutureMonthGranted('month')]
    #[IsGranted(MoodReflectionVoter::VIEW)]
    public function __invoke(#[CurrentUser] User $user, ?DateTimeImmutable $month = null): Response
    {
        $month ??= new DateTimeImmutable('first day of this month', $user->getTimezone())->setTime(0, 0);

        $lastMoods = $this->moodReflectionService->getPreviousDays();
        $moods = $this->moodReflectionService->getByMonth($month);

        return $this->render('moodReflection/index.html.twig', [
            'month' => $month,
            'lastMoods' => $lastMoods,
            'moods' => $moods,
        ]);
    }
}
