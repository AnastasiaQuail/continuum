<?php

declare(strict_types=1);

namespace Continuum\Controller\Reflection;

use Continuum\Entity\User;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class WeeklyController extends AbstractController
{
    public function __construct() {}

    #[Route(path: '/reflection/weekly/{week}', name: 'app_reflection_weekly', methods: ['GET'])]
    public function __invoke(#[CurrentUser] User $user, ?DateTimeImmutable $week = null): Response
    {
        $week ??= new DateTimeImmutable('first day of this month', $user->getTimezone());

        return $this->render('reflection/weekly/index.html.twig', [
            'week' => $week,
        ]);
    }
}
