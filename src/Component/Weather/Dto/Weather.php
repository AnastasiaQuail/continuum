<?php

declare(strict_types=1);

namespace Continuum\Component\Weather\Dto;

use Continuum\Component\Weather\WmoCode;

final readonly class Weather
{
    public function __construct(
        // °C
        public float $temperature,
        public ?WmoCode $code = null,
        public ?Wind $wind = null,
    ) {}
}
