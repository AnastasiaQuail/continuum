<?php

declare(strict_types=1);

namespace Continuum\Component\Weather\Dto;

use Continuum\Component\Weather\WindDirection;

final readonly class Wind
{
    public function __construct(
        // m/s
        public float $speed,
        public WindDirection $direction,
    ) {}
}
