<?php

declare(strict_types=1);

namespace Continuum\Controller\Calendar;

use Continuum\Entity\User;
use Continuum\Security\Authorization\Voter\CalendarVoter;
use Continuum\Service\Calendar\CalendarEventService;
use Continuum\Service\Calendar\UpcomingEventService;
use Continuum\Service\RequestValidator;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class IndexController extends AbstractController
{
    public function __construct(
        private readonly RequestValidator $requestValidator,
        #[Autowire(env: 'string:APP_DATE_START')]
        private readonly string $startDate,
        private readonly UpcomingEventService $upcomingEventService,
        private readonly CalendarEventService $calendarEventService,
    ) {}

    #[Route(path: '/calendar', name: 'app_calendar', methods: ['GET'])]
    public function __invoke(#[CurrentUser] User $user, #[MapQueryParameter] ?int $year = null): Response
    {
        $year ??= (int) new DateTimeImmutable('now', $user->getTimezone())->format('Y');

        if (null !== $error = $this->requestValidator->validateYear($year)) {
            throw new BadRequestHttpException($error);
        }

        if (!$this->isGranted(CalendarVoter::VIEW, $year)) {
            throw $this->createAccessDeniedException();
        }

        $upcomingNotifications = $this->upcomingEventService->getUpcomingNotifications($user);
        $events = $this->calendarEventService->getByYear($user, $year);

        return $this->render('calendar/index.html.twig', [
            'year' => $year,
            'startDay' => new DateTimeImmutable($this->startDate, $user->getTimezone()),
            'upcomingNotifications' => $upcomingNotifications,
            'events' => $events,
        ]);
    }
}
