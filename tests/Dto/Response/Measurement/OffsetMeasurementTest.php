<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Response\Measurement;

use Continuum\Dto\Response\Measurement\OffsetMeasurement;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(OffsetMeasurement::class)]
final class OffsetMeasurementTest extends TestCase
{
    public function testConstructor(): void
    {
        $dto = new OffsetMeasurement(
            offset: 1.0,
            min: 0.1,
            max: 2.0,
        );

        self::assertSame(1.0, $dto->offset);
        self::assertSame(0.1, $dto->min);
        self::assertSame(2.0, $dto->max);
    }
}
