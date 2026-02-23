<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Response\Reflection;

use Continuum\Dto\Response\Reflection\ChartMoodReflection;
use Continuum\Enum\Color;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ChartMoodReflection::class)]
final class ChartMoodReflectionTest extends TestCase
{
    public function testConstructor(): void
    {
        $dto = new ChartMoodReflection(
            type: 1,
            prevTime: 100000,
            time: 5,
            color: Color::Blue,
        );

        self::assertSame(1, $dto->type);
        self::assertSame(100000, $dto->prevTime);
        self::assertSame(5, $dto->time);
        self::assertSame(Color::Blue, $dto->color);
    }
}
