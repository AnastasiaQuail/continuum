<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Response\Workout;

use Continuum\Dto\Response\Workout\ExerciseProgress;
use Continuum\Dto\Response\Workout\WorkoutExerciseProgress;
use Continuum\Entity\Exercise;
use Continuum\Enum\Change;
use Continuum\Enum\ExerciseGroup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(WorkoutExerciseProgress::class)]
final class WorkoutExerciseProgressTest extends TestCase
{
    public function testConstructor(): void
    {
        $exercise = new Exercise(name: 'ex', group: ExerciseGroup::Arms);
        $progress = new ExerciseProgress(change: Change::Increased, percent: 5.0);

        $dto = new WorkoutExerciseProgress($exercise, $progress);

        self::assertSame($progress, $dto->progress);
        self::assertSame($exercise, $dto->exercise);
    }
}
