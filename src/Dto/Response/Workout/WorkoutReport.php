<?php

declare(strict_types=1);

namespace Continuum\Dto\Response\Workout;

final readonly class WorkoutReport
{
    /**
     * @param non-negative-int $count
     * @param non-negative-int $duration
     * @param list<WorkoutExerciseProgress> $progresses
     */
    public function __construct(
        public int $count,
        public int $duration,
        public array $progresses,
    ) {}
}
