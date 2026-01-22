<?php

declare(strict_types=1);

namespace Continuum\Controller\WeeklyReflection;

use Continuum\Form\EditWeeklyReflectionType;
use Continuum\Security\Attribute\IsFutureMonthGranted;
use Continuum\Security\Authorization\Voter\WeeklyReflectionVoter;
use Continuum\Service\WeeklyReflectionService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EditController extends AbstractController
{
    public function __construct(
        private readonly WeeklyReflectionService $weeklyReflectionService,
    ) {}

    #[Route(path: '/weekly-reflections/weeks/{week}', name: 'app_weekly_reflection_edit', methods: ['GET', 'POST'])]
    #[IsFutureMonthGranted('week')]
    #[IsGranted(WeeklyReflectionVoter::EDIT)]
    public function __invoke(Request $request, DateTimeImmutable $week): Response
    {
        $weeklyReflection = $this->weeklyReflectionService->findByWeek($week);

        $form = $this->createForm(EditWeeklyReflectionType::class, options: ['weeklyReflection' => $weeklyReflection]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $weeklyReflection = $this->weeklyReflectionService->save($week, $weeklyReflection, $form->getData());

            $this->addFlash(
                'success',
                sprintf('The weekly reflection for %s was saved.', $weeklyReflection->getDate()->format('j F'))
            );

            return $this->redirectToRoute('app_weekly_reflections', ['month' => $week->format('Y-m')]);
        }

        return $this->render('weeklyReflection/edit.html.twig', [
            'week' => $week,
            'form' => $form,
        ]);
    }
}
