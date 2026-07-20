<?php

declare(strict_types=1);

namespace Continuum\Entity\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class Measurement
{
    public function __construct(
        public bool $isProgressReversed = false,
    ) {}
}
