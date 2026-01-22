<?php

declare(strict_types=1);

namespace Continuum\Dto\Response\Measurement;

final readonly class ChartMeasurement
{
    public const string TYPE_INCREASE = 'increase';
    public const string TYPE_UNCHANGED = 'unchanged';
    public const string TYPE_DECREASE = 'decrease';

    public function __construct(
        public string $type,
        public ?int $prevTime,
        public int $time,
        public float $fat,
        public float $weight,
    ) {}

    public static function first(float $fat, float $weight): self
    {
        return new self(
            type: self::TYPE_UNCHANGED,
            prevTime: null,
            time: 0,
            fat: $fat,
            weight: $weight,
        );
    }
}
