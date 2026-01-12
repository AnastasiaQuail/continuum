<?php

declare(strict_types=1);

namespace Continuum\Controller\Reflection;

use Continuum\Entity\User;
use Continuum\Service\ReflectionMoodService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class MoodController extends AbstractController
{
    public function __construct(
        private readonly ReflectionMoodService $reflectionMoodService,
    ) {}

    #[Route(path: '/reflection/mood/{month}', name: 'app_reflection_mood', methods: ['GET'])]
    public function __invoke(#[CurrentUser] User $user, ?DateTimeImmutable $month = null): Response
    {
        $month ??= new DateTimeImmutable('first day of this month', $user->getTimezone());

        $lastMoods = $this->reflectionMoodService->getPreviousDays();
        $moods = $this->reflectionMoodService->getByMonth($month);

        return $this->render('reflection/mood/index.html.twig', [
            'month' => $month,
            'lastMoods' => $lastMoods,
            'moods' => $moods,
        ]);
    }
}
