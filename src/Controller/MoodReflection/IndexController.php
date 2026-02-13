<?php

declare(strict_types=1);

namespace Continuum\Controller\MoodReflection;

use Continuum\Entity\User;
use Continuum\Security\Authorization\Voter\MoodReflectionVoter;
use Continuum\Service\MoodReflectionService;
use Continuum\Service\RequestValidator;
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
        private readonly MoodReflectionService $moodReflectionService,
    ) {}

    #[Route(path: '/mood-reflections', name: 'app_mood_reflections', methods: ['GET'])]
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

        if (!$this->isGranted(MoodReflectionVoter::VIEW, $month)) {
            throw $this->createAccessDeniedException();
        }

        $lastMoods = $this->moodReflectionService->getPreviousDays();
        $moods = $this->moodReflectionService->getByMonth($month);

        return $this->render('mood_reflection/index.html.twig', [
            'month' => $month,
            'last_moods' => $lastMoods,
            'moods' => $moods,
        ]);
    }
}
