<?php

declare(strict_types=1);

namespace Continuum\Service;

use Continuum\Validator\Year;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class RequestValidator
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {}

    public function validateYear(int $year): ?string
    {
        $errors = $this->validator->validate($year, new Year());

        if ($errors->count() > 0) {
            return (string) $errors->get(0)->getMessage();
        }

        return null;
    }

    public function validateExistenceMonth(DateTimeImmutable $month, DateTimeZone $timeZone): ?string
    {
        if (null !== $error = $this->validateYear((int) $month->format('Y'))) {
            return $error;
        }

        if ('01:00:00:00' !== $month->format('d:H:i:s')) {
            return 'Wrong month format. Allowed only "Y-m" format.';
        }

        if (new DateTimeImmutable('now', $timeZone)->format('Y-m') < $month->format('Y-m')) {
            return 'Future month is not allowed.';
        }

        return null;
    }

    public function validateExistenceWeek(DateTimeImmutable $week, DateTimeZone $timeZone): ?string
    {
        if (null !== $error = $this->validateYear((int) $week->format('Y'))) {
            return $error;
        }

        if ('Sun' !== $week->format('D')) {
            return 'Wrong week format. Allowed only sunday.';
        }

        if ('00:00:00' !== $week->format('H:i:s')) {
            return 'Wrong week format. Allowed only "Y-m-d" format.';
        }

        if (new DateTimeImmutable('sunday', $timeZone)->format('Y-m-d') < $week->format('Y-m-d')) {
            return 'Future week is not allowed.';
        }

        return null;
    }

    public function validateDay(DateTimeImmutable $day): ?string
    {
        if (null !== $error = $this->validateYear((int) $day->format('Y'))) {
            return $error;
        }

        if ('00:00:00' !== $day->format('H:i:s')) {
            return 'Wrong day format. Allowed only "Y-m-d" format.';
        }

        return null;
    }

    public function validateExistenceDay(DateTimeImmutable $day, DateTimeZone $timeZone): ?string
    {
        if (null !== $error = $this->validateDay($day)) {
            return $error;
        }

        if (new DateTimeImmutable('now', $timeZone)->format('Y-m-d') < $day->format('Y-m-d')) {
            return 'Future day is not allowed.';
        }

        return null;
    }
}
