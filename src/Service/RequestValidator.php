<?php

declare(strict_types=1);

namespace Continuum\Service;

use Continuum\Validator\Year;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class RequestValidator
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {}

    public function validateYear(int $year): ?string
    {
        $errors = $this->validator->validate($year, [new Year()]);

        if ($errors->count() > 0) {
            return $errors->get(0)->getMessage();
        }

        return null;
    }
}
