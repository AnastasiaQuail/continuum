<?php

declare(strict_types=1);

namespace Continuum\Dto\Request\Workout;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class NewWorkoutSet
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Range(min: 0, max: 199)]
        public float $weight,
        #[Assert\NotBlank]
        #[Assert\Range(min: 1, max: 99)]
        public int $reps,
        #[SerializedName('warmup')]
        private ?string $isWarmup,
    ) {}

    public function isWarmup(): bool
    {
        return 'on' === $this->isWarmup;
    }
}
