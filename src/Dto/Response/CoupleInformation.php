<?php

declare(strict_types=1);

namespace Continuum\Dto\Response;

use Continuum\Component\Weather\Dto\Weather;
use DateTimeImmutable;

final readonly class CoupleInformation
{
    public function __construct(
        public Weather $weather,
        public DateTimeImmutable $time,
        public Weather $partnerWeather,
        public DateTimeImmutable $partnerTime,
        public CoupleTogetherInformation $together,
        public float $distance,
    ) {}
}
