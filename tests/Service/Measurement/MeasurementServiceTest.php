<?php

declare(strict_types=1);

namespace Continuum\Tests\Service\Measurement;

use Continuum\Dto\Request\Measurement\EditMeasurement;
use Continuum\Dto\Response\Measurement\LastMeasurement;
use Continuum\Entity\BodyMeasurement;
use Continuum\Entity\User;
use Continuum\Repository\BodyMeasurementRepositoryInterface;
use Continuum\Service\GodUserService;
use Continuum\Service\Measurement\MeasurementService;
use DateTimeImmutable;
use DateTimeZone;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(MeasurementService::class)]
final class MeasurementServiceTest extends TestCase
{
    private BodyMeasurementRepositoryInterface&MockObject $repository;
    private MeasurementService $service;

    #[Override]
    protected function setUp(): void
    {
        $this->repository = $this->createMock(BodyMeasurementRepositoryInterface::class);
        $this->service = new MeasurementService(
            new GodUserService(userBirthDate: '2000-01-01', userHeight: 180),
            $this->repository
        );
    }

    public function testGetByMonth(): void
    {
        $user = new User('test@example.com');
        $month = new DateTimeImmutable('2025-01-01');

        $m1 = new BodyMeasurement(age: 25, height: 180);
        $m1->datetime = new DateTimeImmutable('2025-01-05 10:00:00');
        $m1->weight = 75.0;

        $m2 = new BodyMeasurement(age: 25, height: 180);
        $m2->datetime = new DateTimeImmutable('2025-01-15 14:30:00');
        $m2->weight = 76.0;

        $this->repository->expects($this->once())
            ->method('findByRange')
            ->with(
                self::callback(
                    static fn (DateTimeImmutable $d): bool => '2025-01-01 00:00:00' === $d->format('Y-m-d H:i:s')
                ),
                self::callback(
                    static fn (DateTimeImmutable $d): bool => '2025-01-31 23:59:59' === $d->format('Y-m-d H:i:s')
                ),
            )
            ->willReturn([$m1, $m2]);

        $found = $this->service->getByMonth($user, $month);

        self::assertCount(2, $found);
        self::assertSame(
            [75.0, 76.0],
            array_map(static fn (BodyMeasurement $bodyMeasurement): float => $bodyMeasurement->weight, $found),
        );
    }

    public function testGetByMonthEmpty(): void
    {
        $user = new User('test@example.com');
        $month = new DateTimeImmutable('2025-02-01');
        $this->repository->expects($this->once())->method('findByRange')->willReturn([]);

        $found = $this->service->getByMonth($user, $month);

        self::assertCount(0, $found);
    }

    public function testGetByRange(): void
    {
        $user = new User('test@example.com');
        $fromDay = new DateTimeImmutable('2025-01-05');
        $toDay = new DateTimeImmutable('2025-01-10');

        $m1 = new BodyMeasurement(age: 25, height: 180);
        $m1->datetime = new DateTimeImmutable('2025-01-05 10:00:00');
        $m1->weight = 75.0;

        $m2 = new BodyMeasurement(age: 25, height: 180);
        $m2->datetime = new DateTimeImmutable('2025-01-10 16:00:00');
        $m2->weight = 76.0;

        $this->repository->expects($this->once())
            ->method('findByRange')
            ->with(
                self::callback(
                    static fn (DateTimeImmutable $d): bool => '2025-01-05 00:00:00' === $d->format('Y-m-d H:i:s')
                ),
                self::callback(
                    static fn (DateTimeImmutable $d): bool => '2025-01-10 23:59:59' === $d->format('Y-m-d H:i:s')
                ),
            )
            ->willReturn([$m1, $m2]);

        $found = $this->service->getByRange($user, $fromDay, $toDay);

        self::assertCount(2, $found);
        self::assertSame(
            [75.0, 76.0],
            array_map(static fn (BodyMeasurement $bodyMeasurement): float => $bodyMeasurement->weight, $found),
        );
    }

    public function testGetLastMeasurement(): void
    {
        $this->repository->expects($this->once())->method('findOneLastWithNotNull')
            ->willReturn(
                new LastMeasurement(
                    weight: 80.5,
                    neck: 38.0,
                    chest: 100.0,
                    shoulders: 120.0,
                    waist: 85.0,
                    flexedBiceps: 32.0,
                    hips: 95.0,
                    thigh: 58.0,
                    calf: 38.5,
                )
            );

        $found = $this->service->getLastMeasurement();

        self::assertSame(80.5, $found->weight);
        self::assertSame(38.0, $found->neck);
        self::assertSame(100.0, $found->chest);
        self::assertSame(120.0, $found->shoulders);
        self::assertSame(85.0, $found->waist);
        self::assertSame(32.0, $found->flexedBiceps);
        self::assertSame(95.0, $found->hips);
        self::assertSame(58.0, $found->thigh);
        self::assertSame(38.5, $found->calf);
    }

    public function testGetInitMeasurement(): void
    {
        $user = new User('test@example.com');
        $date = new DateTimeImmutable('2025-02-01');

        $measurement = new BodyMeasurement(age: 25, height: 180);
        $measurement->datetime = new DateTimeImmutable('2025-01-15 10:00:00');
        $measurement->weight = 75.0;

        $this->repository->expects($this->once())
            ->method('findOneLastByMonth')
            ->with(
                self::callback(static fn (DateTimeImmutable $d): bool => '2025-01-01' === $d->format('Y-m-d')),
                $user->timezone,
            )
            ->willReturn($measurement);

        $found = $this->service->getInitMeasurement($user, $date);

        self::assertNotNull($found);
        self::assertSame(75.0, $found->weight);
    }

    public function testGetInitMeasurementNull(): void
    {
        $user = new User('test@example.com');
        $date = new DateTimeImmutable('2025-02-01');
        $this->repository->expects($this->once())->method('findOneLastByMonth')->willReturn(null);

        $found = $this->service->getInitMeasurement($user, $date);

        self::assertNull($found);
    }

    public function testSaveCreatesNewMeasurement(): void
    {
        $user = new User('test@example.com');
        $user->password = 'password';
        $user->timezone = new DateTimeZone('UTC');

        $datetime = new DateTimeImmutable('2025-01-15 10:30:00');
        $dto = new EditMeasurement(
            datetime: $datetime,
            weight: 80.0,
            neck: 38.5,
            chest: 100.0,
            shoulders: 120.0,
            waist: 85.0,
            flexedBiceps: 32.0,
            hips: 95.5,
            thigh: 58.0,
            calf: 38.5,
        );

        $this->repository->expects($this->once())->method('save');

        $found = $this->service->save($user, $dto);

        // GodUserService was initialized with birth date 2000-01-01, so age on 2025-01-15 is 25
        self::assertSame(25, $found->age);
        self::assertSame(180, $found->height);
        self::assertSame(80.0, $found->weight);
        self::assertSame(38.5, $found->neck);
        self::assertSame(100.0, $found->chest);
        self::assertSame(120.0, $found->shoulders);
        self::assertSame(85.0, $found->waist);
        self::assertSame(32.0, $found->flexedBiceps);
        self::assertSame(95.5, $found->hips);
        self::assertSame(58.0, $found->thigh);
        self::assertSame(38.5, $found->calf);
    }

    public function testSaveUpdatesExistingMeasurement(): void
    {
        $user = new User('test@example.com');
        $user->password = 'password';

        $existing = new BodyMeasurement(age: 28, height: 175);
        $existing->datetime = new DateTimeImmutable('2025-01-10 08:00:00');
        $existing->weight = 75.0;
        $existing->neck = 37.0;

        $datetime = new DateTimeImmutable('2025-01-15 10:30:00');
        $dto = new EditMeasurement(
            datetime: $datetime,
            weight: 80.0,
            neck: 38.5,
            chest: 100.0,
            shoulders: 120.0,
            waist: 85.0,
            flexedBiceps: 32.0,
            hips: 95.5,
            thigh: 58.0,
            calf: 38.5,
        );

        $this->repository->expects($this->once())->method('save');

        $found = $this->service->save($user, $dto, $existing);

        self::assertSame($existing, $found);
        self::assertSame(28, $found->age);
        self::assertSame(175, $found->height);
        self::assertSame(80.0, $found->weight);
        self::assertSame(38.5, $found->neck);
        self::assertSame(100.0, $found->chest);
        self::assertSame(120.0, $found->shoulders);
        self::assertSame(85.0, $found->waist);
        self::assertSame(32.0, $found->flexedBiceps);
        self::assertSame(95.5, $found->hips);
        self::assertSame(58.0, $found->thigh);
        self::assertSame(38.5, $found->calf);
    }

    public function testSaveWithPartialMeasurements(): void
    {
        $user = new User('test@example.com');
        $user->password = 'password';
        $user->timezone = new DateTimeZone('UTC');

        $datetime = new DateTimeImmutable('2025-01-15 10:30:00');
        $dto = new EditMeasurement(
            datetime: $datetime,
            weight: 80.0,
            neck: 38.5,
            waist: 85.0,
        );

        $this->repository->expects($this->once())->method('save');

        $found = $this->service->save($user, $dto);

        self::assertSame(80.0, $found->weight);
        self::assertSame(38.5, $found->neck);
        self::assertNull($found->chest);
        self::assertSame(85.0, $found->waist);
        self::assertNull($found->flexedBiceps);
    }

    public function testSaveDatetimeConvertedToUtc(): void
    {
        $user = new User('test@example.com');
        $user->password = 'password';
        $user->timezone = new DateTimeZone('UTC');

        $tokyoTz = new DateTimeZone('Asia/Tokyo');
        $datetime = new DateTimeImmutable('2025-01-15 19:30:00', $tokyoTz);

        $dto = new EditMeasurement(
            datetime: $datetime,
            weight: 80.0,
        );

        $this->repository->expects($this->once())->method('save');

        $found = $this->service->save($user, $dto);

        self::assertSame('UTC', $found->datetime->getTimezone()->getName());
        // Tokyo 19:30 = UTC 10:30
        self::assertSame('10:30:00', $found->datetime->format('H:i:s'));
    }
}
