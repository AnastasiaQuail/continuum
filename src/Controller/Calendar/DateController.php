<?php

declare(strict_types=1);

namespace Continuum\Controller\Calendar;

use Continuum\Entity\User;
use Continuum\Form\Calendar\NewCalendarEventType;
use Continuum\Service\CalendarEventService;
use Continuum\Validator\Year;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class DateController extends AbstractController
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly CalendarEventService $calendarEventService,
    ) {}

    #[Route(path: '/calendar/dates/{date}', name: 'app_calendar_date', methods: ['GET', 'POST'])]
    public function __invoke(Request $request, #[CurrentUser] User $user, DateTimeImmutable $date): Response
    {
        $errors = $this->validator->validate((int) $date->format('Y'), [new Year()]);

        if ($errors->count() > 0) {
            throw new BadRequestHttpException($errors->get(0)->getMessage());
        }

        $form = $this->createForm(NewCalendarEventType::class, options: ['timezone' => $user->getTimezone()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = $this->calendarEventService->create($user, $date, $form->getData());

            $this->addFlash('success', sprintf('The "%s" event was created.', $event->getTitle()));

            return $this->redirectToRoute('app_calendar_date', ['date' => $date->format('Y-m-d')]);
        }

        $events = $this->calendarEventService->getByDay($user, $date);

        return $this->render('calendar/date.html.twig', [
            'date' => $date,
            'events' => $events,
            'form' => $form,
        ]);
    }
}
