<?php

declare(strict_types=1);

namespace Continuum\Controller\MoodReflection;

use Continuum\Dto\Request\Reflection\EditMoodReflection;
use Continuum\Entity\User;
use Continuum\Form\EditMoodReflectionType;
use Continuum\Security\Authorization\Voter\MoodReflectionVoter;
use Continuum\Service\MoodReflectionService;
use Continuum\Service\RequestValidator;
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
        private readonly MoodReflectionService $moodReflectionService,
    ) {}

    #[Route(path: '/mood-reflections/{day:date}', name: 'app_mood_reflection_edit', methods: ['GET', 'POST'])]
    #[IsGranted(MoodReflectionVoter::EDIT)]
    public function __invoke(#[CurrentUser] User $user, Request $request, string $date): Response
    {
        $day = new DateTimeImmutable($date, $user->getTimezone())->setTime(0, 0);

        if (null !== $error = $this->requestValidator->validateExistenceDay($day, $user->getTimezone())) {
            throw new BadRequestHttpException($error);
        }

        $moodReflection = $this->moodReflectionService->findMoodByDay($day);

        $form = $this->createForm(EditMoodReflectionType::class, options: ['moodReflection' => $moodReflection]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditMoodReflection $dto */
            $dto = $form->getData();
            $moodReflection = $this->moodReflectionService->save($day, $moodReflection, $dto);

            $this->addFlash(
                'success',
                sprintf('The mood for %s was saved.', $moodReflection->getDate()->format('j F'))
            );

            return $this->redirectToRoute('app_mood_reflections', ['month' => $day->format('Y-m')]);
        }

        return $this->render('moodReflection/edit.html.twig', [
            'day' => $day,
            'form' => $form,
        ]);
    }
}
