<?php

declare(strict_types=1);

namespace Continuum\Tests\Entity;

use Continuum\Entity\Exercise;
use Continuum\Entity\Workout;
use Continuum\Entity\WorkoutExercise;
use Continuum\Enum\ExerciseGroup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV7;

#[CoversClass(WorkoutExercise::class)]
final class WorkoutExerciseTest extends TestCase
{
    public function testCreate(): void
    {
        $workoutExercise = new WorkoutExercise(
            workout: $workout = new Workout(),
            exercise: $exercise = $this->buildExercise(),
        );

        self::assertInstanceOf(UuidV7::class, $workoutExercise->id);
        self::assertSame($workout, $workoutExercise->workout);
        self::assertSame($exercise, $workoutExercise->exercise);
        self::assertSame(0, $workoutExercise->orderIndex);
        self::assertSame('', $workoutExercise->description);
        self::assertTrue($workoutExercise->sets->isEmpty());
    }

    public function testOrderIndex(): void
    {
        $workoutExercise = new WorkoutExercise(
            workout: new Workout(),
            exercise: $this->buildExercise(),
            orderIndex: 2,
        );

        self::assertSame(2, $workoutExercise->orderIndex);
    }

    public function testDescription(): void
    {
        $workoutExercise = new WorkoutExercise(
            workout: new Workout(),
            exercise: $this->buildExercise(),
        );

        $workoutExercise->description = 'example';

        self::assertSame('example', $workoutExercise->description);
    }

    private function buildExercise(): Exercise
    {
        return new Exercise(name: 'ex', group: ExerciseGroup::Arms);
    }
}
