<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Request\Workout;

use Continuum\Dto\Request\Workout\NewWorkoutSet;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(NewWorkoutSet::class)]
final class NewWorkoutSetTest extends TestCase
{
    public function testConstructor(): void
    {
        $dto = new NewWorkoutSet(
            weight: 10.0,
            reps: 5,
            isWarmup: null,
        );

        self::assertSame(10.0, $dto->weight);
        self::assertSame(5, $dto->reps);
        self::assertFalse($dto->isWarmup());
    }

    #[DataProvider('provideWarmupCases')]
    public function testWarmup(string $warmup, bool $isWarmup): void
    {
        $dto = new NewWorkoutSet(
            weight: 10.0,
            reps: 5,
            isWarmup: $warmup,
        );

        self::assertSame($isWarmup, $dto->isWarmup());
    }

    /**
     * @return iterable<array{0: string, 1: bool}>
     */
    public static function provideWarmupCases(): iterable
    {
        yield ['', false];

        yield ['non-on', false];

        yield ['on', true];
    }
}
