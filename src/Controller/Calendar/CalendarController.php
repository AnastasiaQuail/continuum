<?php

declare(strict_types=1);

namespace Continuum\Controller\Calendar;

use Continuum\Entity\User;
use Continuum\Service\CalendarEventService;
use Continuum\Service\UpcomingEventService;
use Continuum\Validator\Year;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CalendarController extends AbstractController
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly UpcomingEventService $upcomingEventService,
        private readonly CalendarEventService $calendarEventService,
    ) {}

    #[Route(path: '/calendar/{year}', name: 'app_calendar', requirements: ['year' => '\d+'], methods: ['GET'])]
    public function __invoke(#[CurrentUser] User $user, ?int $year = null): Response
    {
        if (null !== $year) {
            $errors = $this->validator->validate($year, [new Year()]);

            if ($errors->count() > 0) {
                throw new BadRequestHttpException($errors->get(0)->getMessage());
            }
        }

        $year ??= (int) new DateTimeImmutable('now', $user->getTimezone())->format('Y');
        $startDay = new DateTimeImmutable('2025-03-15');

        $upcomingNotifications = $this->upcomingEventService->getUpcomingNotifications($user);
        $events = $this->calendarEventService->getByYear($user, $year);

        return $this->render('calendar/index.html.twig', [
            'year' => $year,
            'startDay' => $startDay,
            'upcomingNotifications' => $upcomingNotifications,
            'events' => $events,
        ]);
    }
}
