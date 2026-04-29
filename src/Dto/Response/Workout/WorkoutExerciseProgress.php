<?php

declare(strict_types=1);

namespace Continuum\Dto\Response\Workout;

use Continuum\Entity\Exercise;

final readonly class WorkoutExerciseProgress
{
    public function __construct(
        public Exercise $exercise,
        public ?ExerciseProgress $progress = null,
    ) {}
}
