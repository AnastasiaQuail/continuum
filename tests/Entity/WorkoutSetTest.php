<?php

declare(strict_types=1);

namespace Continuum\Tests\Entity;

use Continuum\Entity\Exercise;
use Continuum\Entity\Workout;
use Continuum\Entity\WorkoutExercise;
use Continuum\Entity\WorkoutSet;
use Continuum\Enum\ExerciseGroup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV7;

#[CoversClass(WorkoutSet::class)]
final class WorkoutSetTest extends TestCase
{
    public function testCreate(): void
    {
        $workoutExercise = new WorkoutExercise(
            workout: new Workout(),
            exercise: new Exercise(name: 'ex', group: ExerciseGroup::Arms),
        );

        $workoutSet = new WorkoutSet(
            workoutExercise: $workoutExercise,
            weight: 1.0001,
            reps: 4,
        );

        self::assertInstanceOf(UuidV7::class, $workoutSet->id);
        self::assertSame($workoutExercise, $workoutSet->workoutExercise);
        self::assertSame(1.0, $workoutSet->weight);
        self::assertSame(4, $workoutSet->reps);
        self::assertFalse($workoutSet->isWarmup);
        self::assertSame(0, $workoutSet->orderIndex);
    }

    public function testIsWarmup(): void
    {
        $workoutExercise = new WorkoutExercise(
            workout: new Workout(),
            exercise: new Exercise(name: 'ex', group: ExerciseGroup::Arms),
        );

        $workoutSet = new WorkoutSet(
            workoutExercise: $workoutExercise,
            weight: 9.9999,
            reps: 1,
            isWarmup: true,
        );

        self::assertSame(9.9, $workoutSet->weight);
        self::assertTrue($workoutSet->isWarmup);
    }

    public function testOrderIndex(): void
    {
        $workoutExercise = new WorkoutExercise(
            workout: new Workout(),
            exercise: new Exercise(name: 'ex', group: ExerciseGroup::Arms),
        );

        $workoutSet = new WorkoutSet(
            workoutExercise: $workoutExercise,
            weight: 0.0,
            reps: 1,
            orderIndex: 3,
        );

        self::assertSame(3, $workoutSet->orderIndex);
    }
}
