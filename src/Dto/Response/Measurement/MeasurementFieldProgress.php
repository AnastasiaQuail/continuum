<?php

declare(strict_types=1);

namespace Continuum\Dto\Response\Measurement;

use Continuum\Enum\Change;

final readonly class MeasurementFieldProgress
{
    public function __construct(
        public float $value,
        public Change $progress,
        public bool $isProgressReversed,
    ) {}
}
