<?php

declare(strict_types=1);

namespace Continuum\Dto\Request\Workout;

use Continuum\Enum\ExerciseGroup;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class EditExercise
{
    public function __construct(
        public ExerciseGroup $group,
        #[Assert\Length(max: 255)]
        public string $name,
    ) {}
}
