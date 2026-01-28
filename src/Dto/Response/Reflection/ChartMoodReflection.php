<?php

declare(strict_types=1);

namespace Continuum\Dto\Response\Reflection;

use Continuum\Enum\Color;

final readonly class ChartMoodReflection
{
    public function __construct(
        public int $type,
        public ?int $prevTime,
        public int $time,
        public Color $color,
    ) {}
}
