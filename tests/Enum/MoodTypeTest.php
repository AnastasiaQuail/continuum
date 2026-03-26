<?php

declare(strict_types=1);

namespace Continuum\Tests\Enum;

use Continuum\Enum\Color;
use Continuum\Enum\MoodType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(MoodType::class)]
final class MoodTypeTest extends TestCase
{
    #[DataProvider('provideGetColorCases')]
    public function testGetColor(MoodType $moodType, Color $color): void
    {
        self::assertSame($color, $moodType->getColor());
    }

    /**
     * @return iterable<array{0: MoodType, 1: Color}>
     */
    public static function provideGetColorCases(): iterable
    {
        $colors = [
            Color::Red,
            Color::Yellow,
            Color::Green,
            Color::Sky,
            Color::Indigo,
        ];

        foreach (MoodType::cases() as $moodType) {
            /**
             * @var Color $color
             *
             * @phpstan-ignore varTag.type (it allows for tests)
             */
            $color = array_shift($colors);

            yield [$moodType, $color];
        }
    }
}
