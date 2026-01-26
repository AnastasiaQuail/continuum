<?php

declare(strict_types=1);

namespace Continuum\Controller\Workout;

use Continuum\Entity\User;
use Continuum\Service\RequestValidator;
use Continuum\Service\Workout\WorkoutService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class IndexController extends AbstractController
{
    public function __construct(
        private readonly RequestValidator $requestValidator,
        private readonly WorkoutService $workoutService,
    ) {}

    #[Route(path: '/workouts', name: 'app_workouts', methods: ['GET'])]
    public function __invoke(
        #[CurrentUser] User $user,
        #[MapQueryParameter('month')] ?string $date = null,
    ): Response {
        $month ??= new DateTimeImmutable($date ?? 'first day of this month', $user->getTimezone())->setTime(0, 0);

        if (null !== $error = $this->requestValidator->validateExistenceMonth($month, $user->getTimezone())) {
            throw new BadRequestHttpException($error);
        }

        $workouts = $this->workoutService->getByMonth($user, $month);

        return $this->render('workout/index.html.twig', [
            'month' => $month,
            'workouts' => $workouts,
        ]);
    }
}
