<?php

declare(strict_types=1);

namespace Continuum\Dto\Response\Measurement;

final readonly class LastMeasurement
{
    public function __construct(
        public float $weight,
        public ?float $neck = null,
        public ?float $chest = null,
        public ?float $shoulders = null,
        public ?float $waist = null,
        public ?float $flexedBiceps = null,
        public ?float $hips = null,
        public ?float $thigh = null,
        public ?float $calf = null,
    ) {}
}
