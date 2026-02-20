<?php

declare(strict_types=1);

namespace Continuum\Dto\Response;

use DateInterval;
use Override;
use Stringable;

final readonly class CoupleTogetherInformation implements Stringable
{
    public function __construct(
        private DateInterval $together,
    ) {}

    #[Override]
    public function __toString(): string
    {
        if (1 === $this->together->invert) {
            return '';
        }

        $data = [];
        if ($this->together->y > 0) {
            $data[] = sprintf('%d %s', $this->together->y, 1 === $this->together->y ? 'year' : 'years');
        }

        if ($this->together->m > 0) {
            $data[] = sprintf('%d %s', $this->together->m, 1 === $this->together->m ? 'month' : 'months');
        }

        if ($this->together->d > 0) {
            $data[] = sprintf('%d %s', $this->together->d, 1 === $this->together->d ? 'day' : 'days');
        }

        return match (count($data)) {
            3 => sprintf('%s, %s and %s', $data[0], $data[1], $data[2]),
            2 => sprintf('%s and %s', $data[0], $data[1]),
            1 => $data[0],
            default => '',
        };
    }

    public function isStartDay(): bool
    {
        return 0 === $this->together->d;
    }

    public function getDays(int $days): int
    {
        $togetherDays = $this->together->days;
        if (false === $togetherDays) {
            $togetherDays = 0;
        }

        return $togetherDays < $days ? $togetherDays : $days;
    }
}
