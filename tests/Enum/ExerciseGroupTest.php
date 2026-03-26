<?php

declare(strict_types=1);

namespace Continuum\Tests\Enum;

use Continuum\Enum\Color;
use Continuum\Enum\ExerciseGroup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ExerciseGroup::class)]
final class ExerciseGroupTest extends TestCase
{
    #[DataProvider('provideGetColorCases')]
    public function testGetColor(ExerciseGroup $exerciseGroup, Color $color): void
    {
        self::assertSame($color, $exerciseGroup->getColor());
    }

    /**
     * @return iterable<array{0: ExerciseGroup, 1: Color}>
     */
    public static function provideGetColorCases(): iterable
    {
        $colors = [
            Color::Orange,
            Color::Yellow,
            Color::Green,
            Color::Teal,
            Color::Sky,
            Color::Indigo,
        ];

        foreach (ExerciseGroup::cases() as $exerciseGroup) {
            /**
             * @var Color $color
             *
             * @phpstan-ignore varTag.type (it allows for tests)
             */
            $color = array_shift($colors);

            yield [$exerciseGroup, $color];
        }
    }
}
