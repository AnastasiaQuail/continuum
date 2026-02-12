<?php

declare(strict_types=1);

namespace Continuum\Dto\Response\Measurement;

use Continuum\Enum\Change;

final readonly class ChartMeasurement
{
    public function __construct(
        public Change $type,
        public ?int $prevTime,
        public int $time,
        public float $fat,
        public float $weight,
    ) {}

    public static function first(float $fat, float $weight): self
    {
        return new self(
            type: Change::Unchanged,
            prevTime: null,
            time: 0,
            fat: $fat,
            weight: $weight,
        );
    }
}
