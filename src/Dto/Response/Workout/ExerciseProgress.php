<?php

declare(strict_types=1);

namespace Continuum\Dto\Response\Workout;

use Continuum\Enum\Change;

final readonly class ExerciseProgress
{
    public function __construct(
        public Change $change,
        public float $percent,
    ) {}
}
