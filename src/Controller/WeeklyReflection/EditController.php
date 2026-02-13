<?php

declare(strict_types=1);

namespace Continuum\Controller\WeeklyReflection;

use Continuum\Dto\Request\Reflection\EditWeeklyReflection;
use Continuum\Entity\User;
use Continuum\Form\EditWeeklyReflectionType;
use Continuum\Security\Authorization\Voter\WeeklyReflectionVoter;
use Continuum\Service\RequestValidator;
use Continuum\Service\WeeklyReflectionService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EditController extends AbstractController
{
    public function __construct(
        private readonly RequestValidator $requestValidator,
        private readonly WeeklyReflectionService $weeklyReflectionService,
    ) {}

    #[Route(path: '/weekly-reflections/{week:date}', name: 'app_weekly_reflection_edit', methods: ['GET', 'POST'])]
    #[IsGranted(WeeklyReflectionVoter::EDIT)]
    public function __invoke(#[CurrentUser] User $user, Request $request, string $date): Response
    {
        $week = new DateTimeImmutable($date, $user->getTimezone())->setTime(0, 0);

        if (null !== $error = $this->requestValidator->validateExistenceWeek($week, $user->getTimezone())) {
            throw new BadRequestHttpException($error);
        }

        $weeklyReflection = $this->weeklyReflectionService->findByWeek($week);

        $form = $this->createForm(EditWeeklyReflectionType::class, options: ['weeklyReflection' => $weeklyReflection]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditWeeklyReflection $dto */
            $dto = $form->getData();
            $weeklyReflection = $this->weeklyReflectionService->save($week, $weeklyReflection, $dto);

            $this->addFlash(
                'success',
                sprintf('The weekly reflection for %s was saved.', $weeklyReflection->getDate()->format('j F'))
            );

            return $this->redirectToRoute('app_weekly_reflections', ['month' => $week->format('Y-m')]);
        }

        return $this->render('weekly_reflection/edit.html.twig', [
            'week' => $week,
            'form' => $form,
        ]);
    }
}
