<?php

declare(strict_types=1);

namespace Continuum\Component\Weather\Dto;

final readonly class Wind
{
    public function __construct(
        // km/h
        public float $speed,
        // °
        public float $direction,
    ) {}
}
