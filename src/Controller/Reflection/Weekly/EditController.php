<?php

declare(strict_types=1);

namespace Continuum\Controller\Reflection\Weekly;

use Continuum\Form\EditWeeklyReflectionType;
use Continuum\Service\WeeklyReflectionService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EditController extends AbstractController
{
    public function __construct(
        private readonly WeeklyReflectionService $weeklyReflectionService,
    ) {}

    #[Route(path: '/reflection/weekly/weeks/{week}', name: 'app_weekly_reflection_edit', methods: ['GET', 'POST'])]
    public function __invoke(Request $request, DateTimeImmutable $week): Response
    {
        $weeklyReflection = $this->weeklyReflectionService->findByWeek($week);

        $form = $this->createForm(EditWeeklyReflectionType::class, options: ['weeklyReflection' => $weeklyReflection]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $weeklyReflection = $this->weeklyReflectionService->save($week, $weeklyReflection, $form->getData());

            $this->addFlash(
                'success',
                sprintf('The weekly reflection for %s was updated.', $weeklyReflection->getDate()->format('j F'))
            );

            return $this->redirectToRoute('app_weekly_reflection', ['month' => $week->format('Y-m')]);
        }

        return $this->render('reflection/weekly/edit.html.twig', [
            'week' => $week,
            'form' => $form,
        ]);
    }
}
