<?php

declare(strict_types=1);

namespace Continuum\Tests\Component\Weather;

use Continuum\Component\Weather\WindDirection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(WindDirection::class)]
final class WindDirectionTest extends TestCase
{
    #[DataProvider('provideWindDirectionFromDegreesCases')]
    public function testWindDirectionFromDegrees(int $degrees, WindDirection $expectedDirection): void
    {
        $direction = WindDirection::fromDegrees($degrees);

        self::assertSame($expectedDirection, $direction);
    }

    /**
     * @return iterable<array{int, WindDirection}>
     */
    public static function provideWindDirectionFromDegreesCases(): iterable
    {
        yield [0, WindDirection::North];

        yield [23, WindDirection::NorthEast];

        yield [67, WindDirection::NorthEast];

        yield [80, WindDirection::East];

        yield [150, WindDirection::SouthEast];

        yield [193, WindDirection::South];

        yield [225, WindDirection::SouthWest];

        yield [270, WindDirection::West];

        yield [315, WindDirection::NorthWest];

        yield [360, WindDirection::North];
    }

    /**
     * @param non-empty-string $expectedEmoji
     */
    #[DataProvider('provideWindDirectionEmojiCases')]
    public function testWindDirectionEmoji(WindDirection $direction, string $expectedEmoji): void
    {
        self::assertSame($expectedEmoji, $direction->toEmoji());
    }

    /**
     * @return iterable<array{WindDirection, non-empty-string}>
     */
    public static function provideWindDirectionEmojiCases(): iterable
    {
        yield [WindDirection::North, '⬇️'];

        yield [WindDirection::NorthEast, '↙️'];

        yield [WindDirection::East, '⬅️'];

        yield [WindDirection::SouthEast, '↖️'];

        yield [WindDirection::South, '⬆️'];

        yield [WindDirection::SouthWest, '↗️'];

        yield [WindDirection::West, '➡️'];

        yield [WindDirection::NorthWest, '↘️'];
    }
}
