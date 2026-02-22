<?php

declare(strict_types=1);

namespace Continuum\Tests\Entity;

use Continuum\Entity\BodyMeasurement;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV7;

#[CoversClass(BodyMeasurement::class)]
final class BodyMeasurementTest extends TestCase
{
    public function testCreate(): void
    {
        $bodyMeasurement = new BodyMeasurement(
            age: $age = 25,
            height: $height = 180,
        );

        self::assertInstanceOf(UuidV7::class, $bodyMeasurement->id);
        self::assertSame($age, $bodyMeasurement->age);
        self::assertSame($height, $bodyMeasurement->height);
        self::assertSame(new DateTimeImmutable()->format('Y-m-d H:i'), $bodyMeasurement->datetime->format('Y-m-d H:i'));
        self::assertSame(0.0, $bodyMeasurement->fatDeurenberg);
        self::assertNull($bodyMeasurement->fatUsNavy);
        self::assertSame(0.0, $bodyMeasurement->weight);
        self::assertNull($bodyMeasurement->neck);
        self::assertNull($bodyMeasurement->chest);
        self::assertNull($bodyMeasurement->shoulders);
        self::assertNull($bodyMeasurement->waist);
        self::assertNull($bodyMeasurement->flexedBiceps);
        self::assertNull($bodyMeasurement->hips);
        self::assertNull($bodyMeasurement->thigh);
        self::assertNull($bodyMeasurement->calf);
    }

    public function testWeight(): void
    {
        $bodyMeasurement = new BodyMeasurement(age: 30, height: 175);
        $bodyMeasurement->weight = 80.5;

        self::assertSame(80.5, $bodyMeasurement->weight);
    }

    public function testNeck(): void
    {
        $bodyMeasurement = new BodyMeasurement(age: 28, height: 182);
        $bodyMeasurement->neck = 38.5;

        self::assertSame(38.5, $bodyMeasurement->neck);
    }

    public function testChest(): void
    {
        $bodyMeasurement = new BodyMeasurement(age: 28, height: 182);
        $bodyMeasurement->chest = 100.0;

        self::assertSame(100.0, $bodyMeasurement->chest);
    }

    public function testShoulders(): void
    {
        $bodyMeasurement = new BodyMeasurement(age: 28, height: 182);
        $bodyMeasurement->shoulders = 120.5;

        self::assertSame(120.5, $bodyMeasurement->shoulders);
    }

    public function testWaist(): void
    {
        $bodyMeasurement = new BodyMeasurement(age: 28, height: 182);
        $bodyMeasurement->waist = 85.0;

        self::assertSame(85.0, $bodyMeasurement->waist);
    }

    public function testFlexedBiceps(): void
    {
        $bodyMeasurement = new BodyMeasurement(age: 28, height: 182);
        $bodyMeasurement->flexedBiceps = 32.0;

        self::assertSame(32.0, $bodyMeasurement->flexedBiceps);
    }

    public function testHips(): void
    {
        $bodyMeasurement = new BodyMeasurement(age: 28, height: 182);
        $bodyMeasurement->hips = 95.5;

        self::assertSame(95.5, $bodyMeasurement->hips);
    }

    public function testThigh(): void
    {
        $bodyMeasurement = new BodyMeasurement(age: 28, height: 182);
        $bodyMeasurement->thigh = 58.0;

        self::assertSame(58.0, $bodyMeasurement->thigh);
    }

    public function testCalf(): void
    {
        $bodyMeasurement = new BodyMeasurement(age: 28, height: 182);
        $bodyMeasurement->calf = 38.5;

        self::assertSame(38.5, $bodyMeasurement->calf);
    }

    #[DataProvider('provideCalculateFatDeurenbergCases')]
    public function testCalculateFatDeurenberg(int $age, int $height, float $weight, float $expectedFat): void
    {
        $bodyMeasurement = new BodyMeasurement(age: $age, height: $height);
        $bodyMeasurement->weight = $weight;
        $bodyMeasurement->calculateFat();

        self::assertSame($expectedFat, $bodyMeasurement->fatDeurenberg);
    }

    /**
     * @return iterable<array{0: int, 1: int, 2: float, 3: float}>
     */
    public static function provideCalculateFatDeurenbergCases(): iterable
    {
        yield [25, 180, 75.0, 17.33];

        yield [30, 175, 80.0, 22.05];

        yield [40, 185, 90.0, 24.56];
    }

    #[DataProvider('provideCalculateFatUsNavyCases')]
    public function testCalculateFatUsNavy(int $height, float $neck, float $waist, float $expectedFat): void
    {
        $bodyMeasurement = new BodyMeasurement(age: 25, height: $height);
        $bodyMeasurement->neck = $neck;
        $bodyMeasurement->waist = $waist;
        $bodyMeasurement->calculateFat();

        self::assertSame($expectedFat, $bodyMeasurement->fatUsNavy);
    }

    /**
     * @return iterable<array{0: int, 1: int, 2: float, 3: float}>
     */
    public static function provideCalculateFatUsNavyCases(): iterable
    {
        yield [175, 35, 100, 29.08];

        yield [180, 40, 80, 10.32];

        yield [185, 43, 90, 15.3];
    }

    public function testCalculateFatUsNavyWithoutNeck(): void
    {
        $bodyMeasurement = new BodyMeasurement(age: 25, height: 180);
        $bodyMeasurement->waist = 85.0;
        // neck is null
        $bodyMeasurement->calculateFat();

        self::assertNull($bodyMeasurement->fatUsNavy);
    }

    public function testCalculateFatUsNavyWithoutWaist(): void
    {
        $bodyMeasurement = new BodyMeasurement(age: 25, height: 180);
        $bodyMeasurement->neck = 38.0;
        // waist is null
        $bodyMeasurement->calculateFat();

        self::assertNull($bodyMeasurement->fatUsNavy);
    }
}
