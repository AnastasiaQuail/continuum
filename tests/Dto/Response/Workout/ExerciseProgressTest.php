<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Response\Workout;

use Continuum\Dto\Response\Workout\ExerciseProgress;
use Continuum\Enum\Change;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ExerciseProgress::class)]
final class ExerciseProgressTest extends TestCase
{
    public function testConstructor(): void
    {
        $dto = new ExerciseProgress(
            change: Change::Increased,
            percent: 12.5,
        );

        self::assertSame(Change::Increased, $dto->change);
        self::assertSame(12.5, $dto->percent);
    }

    public function testConstructorWithDecreased(): void
    {
        $dto = new ExerciseProgress(
            change: Change::Decreased,
            percent: -20.1,
        );

        self::assertSame(Change::Decreased, $dto->change);
        self::assertSame(-20.1, $dto->percent);
    }
}
