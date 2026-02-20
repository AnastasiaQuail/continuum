<?php

declare(strict_types=1);

namespace Continuum\Dto\Response\Measurement;

final readonly class OffsetMeasurement
{
    public function __construct(
        public float $offset,
        public float $min,
        public float $max,
    ) {}
}
