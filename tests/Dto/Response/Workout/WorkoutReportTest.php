<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Response\Workout;

use Continuum\Dto\Response\Workout\ExerciseProgress;
use Continuum\Dto\Response\Workout\WorkoutExerciseProgress;
use Continuum\Dto\Response\Workout\WorkoutReport;
use Continuum\Entity\Exercise;
use Continuum\Enum\Change;
use Continuum\Enum\ExerciseGroup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(WorkoutReport::class)]
final class WorkoutReportTest extends TestCase
{
    public function testConstructor(): void
    {
        $progress = new WorkoutExerciseProgress(
            exercise: new Exercise(name: 'ex', group: ExerciseGroup::Arms),
            progress: new ExerciseProgress(change: Change::Increased, percent: 5.0),
        );

        $dto = new WorkoutReport(
            count: 1,
            duration: 65,
            progresses: [$progress],
        );

        self::assertSame(1, $dto->count);
        self::assertSame(65, $dto->duration);
        self::assertSame([$progress], $dto->progresses);
    }
}
