<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Response\Measurement;

use Continuum\Dto\Response\Measurement\ChartMeasurement;
use Continuum\Enum\Change;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ChartMeasurement::class)]
final class ChartMeasurementTest extends TestCase
{
    public function testConstructor(): void
    {
        $dto = new ChartMeasurement(
            type: Change::Increased,
            prevTime: 1000000,
            time: 1200000,
            fat: 25.1,
            weight: 74.2
        );

        self::assertSame(Change::Increased, $dto->type);
        self::assertSame(1000000, $dto->prevTime);
        self::assertSame(1200000, $dto->time);
        self::assertSame(25.1, $dto->fat);
        self::assertSame(74.2, $dto->weight);
    }

    public function testFirstFactory(): void
    {
        $dto = ChartMeasurement::first(
            fat: 18.0,
            weight: 70.8,
        );

        self::assertSame(Change::Unchanged, $dto->type);
        self::assertNull($dto->prevTime);
        self::assertSame(0, $dto->time);
        self::assertSame(18.0, $dto->fat);
        self::assertSame(70.8, $dto->weight);
    }
}
