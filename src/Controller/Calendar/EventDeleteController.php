<?php

declare(strict_types=1);

namespace Continuum\Controller\Calendar;

use Continuum\Entity\CalendarEvent;
use Continuum\Entity\User;
use Continuum\Service\CalendarEventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class EventDeleteController extends AbstractController
{
    public function __construct(
        private readonly CalendarEventService $calendarEventService,
    ) {}

    #[Route(path: '/calendar/events/{id}', name: 'app_calendar_event_delete', methods: ['DELETE'])]
    public function __invoke(#[CurrentUser] User $user, CalendarEvent $event): Response
    {
        $this->calendarEventService->delete($event);

        $this->addFlash('success', sprintf('Event "%s" has been deleted.', $event->getTitle()));

        return $this->redirectToRoute('app_calendar_date', [
            'date' => $event->getDatetime()->setTimezone($user->getTimezone())->format('Y-m-d')
        ]);
    }
}
