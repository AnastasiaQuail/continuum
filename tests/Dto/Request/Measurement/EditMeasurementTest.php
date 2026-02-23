<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Request\Measurement;

use Continuum\Dto\Request\Measurement\EditMeasurement;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

#[CoversClass(EditMeasurement::class)]
final class EditMeasurementTest extends TestCase
{
    public function testConstructor(): void
    {
        $dto = new EditMeasurement(
            datetime: $time = new DateTimeImmutable(),
            weight: 70.1,
            neck: 39.0,
            chest: 81.5,
            shoulders: 102.2,
            waist: 85.3,
            flexedBiceps: 28.8,
            hips: 90.0,
            thigh: 50.0,
            calf: 35.0,
        );

        self::assertSame($time, $dto->datetime);
        self::assertSame(70.1, $dto->weight);
        self::assertSame(39.0, $dto->neck);
        self::assertSame(81.5, $dto->chest);
        self::assertSame(102.2, $dto->shoulders);
        self::assertSame(85.3, $dto->waist);
        self::assertSame(28.8, $dto->flexedBiceps);
        self::assertSame(90.0, $dto->hips);
        self::assertSame(50.0, $dto->thigh);
        self::assertSame(35.0, $dto->calf);
    }

    public function testConstructorWithNullables(): void
    {
        $dto = new EditMeasurement(
            datetime: new DateTimeImmutable(),
            weight: 70.1,
        );

        self::assertNull($dto->neck);
        self::assertNull($dto->chest);
        self::assertNull($dto->shoulders);
        self::assertNull($dto->waist);
        self::assertNull($dto->flexedBiceps);
        self::assertNull($dto->hips);
        self::assertNull($dto->thigh);
        self::assertNull($dto->calf);
    }

    #[DataProvider('provideValidateCases')]
    public function testValidate(?float $neck, ?float $waist, ?string $path): void
    {
        $dto = new EditMeasurement(
            datetime: new DateTimeImmutable(),
            weight: 70.0,
            neck: $neck,
            waist: $waist,
        );

        $context = $this->createMock(ExecutionContextInterface::class);
        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);

        if (null === $path) {
            $context->expects($this->never())->method('buildViolation');
            $builder->expects($this->never())->method('atPath');
        } else {
            $context->expects($this->once())->method('buildViolation')->willReturn($builder);
            $builder->expects($this->once())->method('atPath')->with($path);
        }

        $dto->validate($context);
    }

    /**
     * @return iterable<array{0: null|float, 1: null|float, 2: null|string}>
     */
    public static function provideValidateCases(): iterable
    {
        yield [40.0, 84.2, null];

        yield [40.0, null, 'waist'];

        yield [null, 84.2, 'neck'];
    }
}
