<?php

declare(strict_types=1);

namespace Continuum\Controller\Calendar;

use Continuum\Dto\Request\Calendar\NewCalendarEvent;
use Continuum\Entity\User;
use Continuum\Form\NewCalendarEventType;
use Continuum\Security\Authorization\Voter\CalendarVoter;
use Continuum\Service\Calendar\CalendarEventService;
use Continuum\Service\RequestValidator;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DayController extends AbstractController
{
    public function __construct(
        private readonly RequestValidator $requestValidator,
        private readonly CalendarEventService $calendarEventService,
    ) {}

    #[Route(path: '/calendar/{day:date}', name: 'app_calendar_day', methods: ['GET', 'POST'])]
    #[IsGranted(CalendarVoter::EDIT)]
    public function __invoke(#[CurrentUser] User $user, Request $request, string $date): Response
    {
        $day = new DateTimeImmutable($date, $user->timezone)->setTime(0, 0);

        if (null !== $error = $this->requestValidator->validateDay($day)) {
            throw new BadRequestHttpException($error);
        }

        $form = $this->createForm(NewCalendarEventType::class, options: ['timezone' => $user->timezone]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var NewCalendarEvent $dto */
            $dto = $form->getData();
            $event = $this->calendarEventService->create($user, $day, $dto);

            $this->addFlash('success', sprintf('The "%s" event was created.', $event->title));

            return $this->redirectToRoute('app_calendar_day', ['day' => $day->format('Y-m-d')]);
        }

        $events = $this->calendarEventService->getByDay($user, $day);

        return $this->render('calendar/day.html.twig', [
            'day' => $day,
            'events' => $events,
            'form' => $form,
        ]);
    }
}
