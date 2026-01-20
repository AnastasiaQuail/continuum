<?php

declare(strict_types=1);

namespace Continuum\Controller\Reflection\Weekly;

use Continuum\Entity\User;
use Continuum\Security\Attribute\IsFutureMonthGranted;
use Continuum\Security\Authorization\Voter\WeeklyReflectionVoter;
use Continuum\Service\WeeklyReflectionService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class WeeklyController extends AbstractController
{
    public function __construct(
        private readonly WeeklyReflectionService $weeklyReflectionService,
    ) {}

    #[Route(path: '/reflection/weekly/{month}', name: 'app_weekly_reflection', methods: ['GET'])]
    #[IsFutureMonthGranted('month')]
    #[IsGranted(WeeklyReflectionVoter::VIEW, 'month')]
    public function __invoke(#[CurrentUser] User $user, ?DateTimeImmutable $month = null): Response
    {
        $month ??= new DateTimeImmutable('first day of this month', $user->getTimezone())->setTime(0, 0);

        $weeklyReflections = $this->weeklyReflectionService->getByMonth($month);

        return $this->render('reflection/weekly/index.html.twig', [
            'month' => $month,
            'weeklyReflections' => $weeklyReflections,
        ]);
    }
}
