<?php

declare(strict_types=1);

namespace Continuum\Dto\Response\Health;

final readonly class LastBodyMeasurement
{
    public function __construct(
        public int $age,
        public int $height,
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
