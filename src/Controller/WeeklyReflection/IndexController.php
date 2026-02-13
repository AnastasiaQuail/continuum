<?php

declare(strict_types=1);

namespace Continuum\Controller\WeeklyReflection;

use Continuum\Entity\User;
use Continuum\Security\Authorization\Voter\WeeklyReflectionVoter;
use Continuum\Service\RequestValidator;
use Continuum\Service\WeeklyReflectionService;
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
        private readonly WeeklyReflectionService $weeklyReflectionService,
    ) {}

    #[Route(path: '/weekly-reflections', name: 'app_weekly_reflections', methods: ['GET'])]
    public function __invoke(
        #[CurrentUser]
        User $user,
        #[MapQueryParameter('month')]
        ?string $date = null,
    ): Response {
        $month = new DateTimeImmutable($date ?? 'first day of this month', $user->getTimezone())->setTime(0, 0);

        if (null !== $error = $this->requestValidator->validateExistenceMonth($month, $user->getTimezone())) {
            throw new BadRequestHttpException($error);
        }

        if (!$this->isGranted(WeeklyReflectionVoter::VIEW, $month)) {
            throw $this->createAccessDeniedException();
        }

        $weeklyReflections = $this->weeklyReflectionService->getByMonth($month);

        return $this->render('weekly_reflection/index.html.twig', [
            'month' => $month,
            'weekly_reflections' => $weeklyReflections,
        ]);
    }
}
