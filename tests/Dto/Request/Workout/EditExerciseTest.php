<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Request\Workout;

use Continuum\Dto\Request\Workout\EditExercise;
use Continuum\Enum\ExerciseGroup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EditExercise::class)]
final class EditExerciseTest extends TestCase
{
    public function testConstructor(): void
    {
        $dto = new EditExercise(
            group: ExerciseGroup::Arms,
            name: 'Some exercise',
        );

        self::assertSame(ExerciseGroup::Arms, $dto->group);
        self::assertSame('Some exercise', $dto->name);
    }
}
