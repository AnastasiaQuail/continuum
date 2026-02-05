<?php

declare(strict_types=1);

namespace Continuum\Dto\Response;

use Continuum\Component\Weather\Dto\Weather;
use DateInterval;
use DateTimeImmutable;

final readonly class CoupleInformation
{
    public function __construct(
        public Weather $weather,
        public DateTimeImmutable $time,
        public Weather $partnerWeather,
        public DateTimeImmutable $partnerTime,
        public float $distance,
        public DateInterval $together,
    ) {}

    public function getTogetherString(): string
    {
        $data = [];
        if ($this->together->y > 0) {
            $data[] = sprintf('%d %s', $this->together->y, $this->together->y === 1 ? 'year' : 'years');
        }
        if ($this->together->m > 0) {
            $data[] = sprintf('%d %s', $this->together->m, $this->together->m === 1 ? 'month' : 'months');
        }
        if ($this->together->d > 0) {
            $data[] = sprintf('%d %s', $this->together->d, $this->together->d === 1 ? 'day' : 'days');
        }

        return match (count($data)) {
            3 => sprintf('%s, %s and %s', $data[0], $data[1], $data[2]),
            2 => sprintf('%s and %s', $data[0], $data[1]),
            1 => $data[0],
            default => '',
        };
    }
}
