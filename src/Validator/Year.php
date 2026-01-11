<?php

declare(strict_types=1);

namespace Continuum\Validator;

use Attribute;
use Symfony\Component\Validator\Constraints\Range;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Year extends Range
{
    public function __construct(int $min = 1000, int $max = 9999)
    {
        parent::__construct(
            notInRangeMessage: 'The year should be between {{ min }} and {{ max }}.',
            min: $min,
            max: $max,
        );
    }
}
