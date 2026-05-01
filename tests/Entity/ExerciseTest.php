<?php

declare(strict_types=1);

namespace Continuum\Tests\Entity;

use Continuum\Entity\Exercise;
use Continuum\Enum\ExerciseGroup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV7;

#[CoversClass(Exercise::class)]
final class ExerciseTest extends TestCase
{
    public function testCreate(): void
    {
        $exercise = new Exercise(
            name: 'exaMplE DÖLÖR',
            group: ExerciseGroup::Arms,
        );

        self::assertInstanceOf(UuidV7::class, $exercise->id);
        self::assertSame('Example dölör', $exercise->name);
        self::assertSame(ExerciseGroup::Arms, $exercise->group);
        self::assertTrue($exercise->isActive);
        self::assertTrue($exercise->workoutExercises->isEmpty());
    }

    public function testActiveDisabled(): void
    {
        $exercise = new Exercise(
            name: 'ex',
            group: ExerciseGroup::Arms,
            isActive: false,
        );

        self::assertFalse($exercise->isActive);
    }
}
