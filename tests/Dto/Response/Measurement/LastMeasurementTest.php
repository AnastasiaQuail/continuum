<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Response\Measurement;

use Continuum\Dto\Response\Measurement\LastMeasurement;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LastMeasurement::class)]
final class LastMeasurementTest extends TestCase
{
    public function testConstructor(): void
    {
        $dto = new LastMeasurement(
            weight: 80.0,
            neck: 37.2,
            chest: 85.8,
            shoulders: 110.5,
            waist: 90.0,
            flexedBiceps: 35.0,
            hips: 95.0,
            thigh: 55.0,
            calf: 31.0,
        );

        self::assertSame(80.0, $dto->weight);
        self::assertSame(37.2, $dto->neck);
        self::assertSame(85.8, $dto->chest);
        self::assertSame(110.5, $dto->shoulders);
        self::assertSame(90.0, $dto->waist);
        self::assertSame(35.0, $dto->flexedBiceps);
        self::assertSame(95.0, $dto->hips);
        self::assertSame(55.0, $dto->thigh);
        self::assertSame(31.0, $dto->calf);
    }

    public function testConstructorWithNullables(): void
    {
        $dto = new LastMeasurement(
            weight: 80.0,
        );

        self::assertSame(80.0, $dto->weight);
        self::assertNull($dto->neck);
        self::assertNull($dto->chest);
        self::assertNull($dto->shoulders);
        self::assertNull($dto->waist);
        self::assertNull($dto->flexedBiceps);
        self::assertNull($dto->hips);
        self::assertNull($dto->thigh);
        self::assertNull($dto->calf);
    }
}
