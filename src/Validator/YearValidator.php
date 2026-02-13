<?php

declare(strict_types=1);

namespace Continuum\Validator;

use Override;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\RangeValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class YearValidator extends RangeValidator
{
    public function __construct(
        #[Autowire(env: 'int:APP_YEAR_MIN')]
        private readonly int $min,
        #[Autowire(env: 'int:APP_YEAR_MAX')]
        private readonly int $max,
        ?PropertyAccessorInterface $propertyAccessor = null
    ) {
        parent::__construct($propertyAccessor);
    }

    #[Override]
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Year) {
            throw new UnexpectedTypeException($constraint, Year::class);
        }

        if (null === $value) {
            return;
        }

        $constraint->min = $this->min;
        $constraint->max = $this->max;

        parent::validate($value, $constraint);
    }
}
