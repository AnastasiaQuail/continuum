<?php

declare(strict_types=1);

namespace Continuum\Controller\Calendar;

use Continuum\Entity\CalendarEvent;
use Continuum\Entity\User;
use Continuum\Security\Authorization\Voter\CalendarVoter;
use Continuum\Service\Calendar\CalendarEventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EventDeleteController extends AbstractController
{
    public function __construct(
        private readonly CalendarEventService $calendarEventService,
    ) {}

    #[Route(path: '/calendar/events/{id}', name: 'app_calendar_event_delete', methods: ['DELETE'])]
    #[IsGranted(CalendarVoter::EVENT_DELETE)]
    public function __invoke(#[CurrentUser] User $user, CalendarEvent $event): RedirectResponse
    {
        $this->calendarEventService->delete($event);

        $this->addFlash('success', sprintf('Event "%s" has been deleted.', $event->title));

        return $this->redirectToRoute('app_calendar_day', [
            'day' => $event->getUserDatetime($user)->format('Y-m-d'),
        ]);
    }
}
