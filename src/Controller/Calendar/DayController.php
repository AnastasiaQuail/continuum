<?php

declare(strict_types=1);

namespace Continuum\Controller\Calendar;

use Continuum\Entity\User;
use Continuum\Form\Calendar\NewCalendarEventType;
use Continuum\Service\CalendarEventService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class DayController extends AbstractController
{
    public function __construct(
        private readonly CalendarEventService $calendarEventService,
    ) {}

    #[Route(path: '/calendar/dates/{day}', name: 'app_calendar_day', methods: ['GET', 'POST'])]
    public function __invoke(Request $request, #[CurrentUser] User $user, DateTimeImmutable $day): Response
    {
        $form = $this->createForm(NewCalendarEventType::class, options: ['timezone' => $user->getTimezone()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = $this->calendarEventService->create($user, $day, $form->getData());

            $this->addFlash('success', sprintf('The "%s" event was created.', $event->getTitle()));

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
