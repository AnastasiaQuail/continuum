<?php

declare(strict_types=1);

namespace Continuum\EventSubscriber;

use Continuum\Entity\User;
use Continuum\Validator\Year;
use DateTimeImmutable;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class DateTimeValidControllerArgumentListener
{
    public function __construct(
        private Security $security,
        private ValidatorInterface $validator,
    ) {}

    #[AsEventListener(priority: -1)]
    public function __invoke(ControllerArgumentsEvent $event): void
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        $arguments = $event->getNamedArguments();
        $isChanged = false;

        foreach ($arguments as $name => $argument) {
            if ($name === 'year' && is_numeric($argument)) {
                $this->validateYear((int) $argument);

                continue;
            }

            if ($argument instanceof DateTimeImmutable) {
                match ($name) {
                    'week' => $this->validateWeek($argument),
                    'day' => $this->validateDay($argument),
                    default => $this->validateYear((int) $argument->format('Y')),
                };

                $arguments[$name] = new DateTimeImmutable(
                    sprintf(
                        '%s-%s-%s %s:%s:%s',
                        $argument->format('Y'),
                        $argument->format('m'),
                        $argument->format('d'),
                        $argument->format('H'),
                        $argument->format('i'),
                        $argument->format('s'),
                    ),
                    $user->getTimezone(),
                );
                $isChanged = true;
            }
        }

        if ($isChanged) {
            $event->setArguments(array_values($arguments));
        }
    }

    private function validateYear(int $year): void
    {
        $errors = $this->validator->validate($year, [new Year()]);

        if ($errors->count() > 0) {
            throw new BadRequestHttpException($errors->get(0)->getMessage());
        }
    }

    private function validateWeek(DateTimeImmutable $week): void
    {
        $this->validateYear((int) $week->format('Y'));

        if ($week->format('d:H:i:s') !== '01:00:00:00') {
            throw new BadRequestHttpException('Wrong week format. Allowed only "Y-m" format.');
        }
    }

    private function validateDay(DateTimeImmutable $day): void
    {
        $this->validateYear((int) $day->format('Y'));

        if ($day->format('H:i:s') !== '00:00:00') {
            throw new BadRequestHttpException('Wrong day format. Allowed only "Y-m-d" format.');
        }
    }
}
