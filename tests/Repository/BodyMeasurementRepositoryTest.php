<?php

declare(strict_types=1);

namespace Continuum\Tests\Repository;

use Continuum\Entity\BodyMeasurement;
use Continuum\Repository\BodyMeasurementRepository;
use Continuum\Tests\Test\AbstractRepositoryTestCase;
use DateTimeImmutable;
use DateTimeZone;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(BodyMeasurementRepository::class)]
final class BodyMeasurementRepositoryTest extends AbstractRepositoryTestCase
{
    private BodyMeasurementRepository $repository;

    #[Override]
    protected function setUp(): void
    {
        $this->repository = self::getContainer()->get(BodyMeasurementRepository::class);
    }

    public function testFindByRange(): void
    {
        $earlyMeasurement = new BodyMeasurement(age: 25, height: 180);
        $earlyMeasurement->datetime = new DateTimeImmutable('2025-01-05 10:00:00');
        $earlyMeasurement->weight = 75.0;

        $middleMeasurement = new BodyMeasurement(age: 25, height: 180);
        $middleMeasurement->datetime = new DateTimeImmutable('2025-01-15 14:30:00');
        $middleMeasurement->weight = 78.0;

        $lateMeasurement = new BodyMeasurement(age: 25, height: 180);
        $lateMeasurement->datetime = new DateTimeImmutable('2025-01-25 16:45:00');
        $lateMeasurement->weight = 76.0;

        foreach ([$earlyMeasurement, $middleMeasurement, $lateMeasurement] as $measurement) {
            $this->repository->save($measurement);
        }

        $from = new DateTimeImmutable('2025-01-01 00:00:00');
        $to = new DateTimeImmutable('2025-01-31 23:59:59');

        $found = $this->repository->findByRange($from, $to);

        self::assertCount(3, $found);
        self::assertSame(
            [75.0, 78.0, 76.0],
            array_map(
                static fn (BodyMeasurement $measurement): float => $measurement->weight,
                $found
            ),
        );
    }

    public function testFindByRangeEmpty(): void
    {
        $measurement = new BodyMeasurement(age: 25, height: 180);
        $measurement->datetime = new DateTimeImmutable('2025-02-05 10:00:00');
        $measurement->weight = 75.0;

        $this->repository->save($measurement);

        $from = new DateTimeImmutable('2025-01-01 00:00:00');
        $to = new DateTimeImmutable('2025-01-31 23:59:59');

        $found = $this->repository->findByRange($from, $to);

        self::assertCount(0, $found);
    }

    public function testFindByRangeOrdered(): void
    {
        // Create measurements in reverse order
        $dates = [
            new DateTimeImmutable('2025-01-25 16:45:00'),
            new DateTimeImmutable('2025-01-05 10:00:00'),
            new DateTimeImmutable('2025-01-15 14:30:00'),
        ];
        foreach ($dates as $idx => $date) {
            $measurement = new BodyMeasurement(age: 25, height: 180);
            $measurement->datetime = $date;
            $measurement->weight = 70.0 + $idx;

            $this->repository->save($measurement);
        }

        $from = new DateTimeImmutable('2025-01-01 00:00:00');
        $to = new DateTimeImmutable('2025-01-31 23:59:59');

        $found = $this->repository->findByRange($from, $to);

        // Should be ordered by datetime ascending
        self::assertCount(3, $found);
        self::assertSame(
            ['2025-01-05', '2025-01-15', '2025-01-25'],
            array_map(
                static fn (BodyMeasurement $measurement): string => $measurement->datetime->format('Y-m-d'),
                $found
            ),
        );
    }

    public function testFindOneLastByMonth(): void
    {
        $month = new DateTimeImmutable('2025-01-01');
        $timeZone = new DateTimeZone('UTC');

        $earlyMeasurement = new BodyMeasurement(age: 25, height: 180);
        $earlyMeasurement->datetime = new DateTimeImmutable('2025-01-05 10:00:00');
        $earlyMeasurement->weight = 75.0;

        $middleMeasurement = new BodyMeasurement(age: 25, height: 180);
        $middleMeasurement->datetime = new DateTimeImmutable('2025-01-25 14:30:00');
        $middleMeasurement->weight = 76.0;

        $lateMeasurement = new BodyMeasurement(age: 25, height: 180);
        $lateMeasurement->datetime = new DateTimeImmutable('2025-01-15 16:45:00');
        $lateMeasurement->weight = 78.0;

        foreach ([$earlyMeasurement, $middleMeasurement, $lateMeasurement] as $measurement) {
            $this->repository->save($measurement);
        }

        $found = $this->repository->findOneLastByMonth($month, $timeZone);

        self::assertNotNull($found);
        self::assertSame(76.0, $found->weight);
        self::assertSame('2025-01-25', $found->datetime->format('Y-m-d'));
    }

    public function testFindOneLastByMonthEmpty(): void
    {
        $month = new DateTimeImmutable('2025-02-01');
        $timeZone = new DateTimeZone('UTC');

        $measurement = new BodyMeasurement(age: 25, height: 180);
        $measurement->datetime = new DateTimeImmutable('2025-01-15 10:00:00');
        $measurement->weight = 75.0;

        $this->repository->save($measurement);

        $found = $this->repository->findOneLastByMonth($month, $timeZone);

        self::assertNull($found);
    }

    public function testFindOneLastWithNotNull(): void
    {
        // Create measurements with different fields set
        $m1 = new BodyMeasurement(age: 25, height: 180);
        $m1->datetime = new DateTimeImmutable('2025-01-05 10:00:00');
        $m1->weight = 75.0;
        $m1->neck = 38.0;

        $this->repository->save($m1);

        $m2 = new BodyMeasurement(age: 25, height: 180);
        $m2->datetime = new DateTimeImmutable('2025-01-10 14:30:00');
        $m2->weight = 76.0;
        $m2->waist = 85.0;

        $this->repository->save($m2);

        $m3 = new BodyMeasurement(age: 25, height: 180);
        $m3->datetime = new DateTimeImmutable('2025-01-15 16:45:00');
        $m3->weight = 77.0;
        $m3->chest = 100.0;
        $m3->hips = 95.0;

        $this->repository->save($m3);

        $lastMeasurement = $this->repository->findOneLastWithNotNull();

        self::assertSame(77.0, $lastMeasurement->weight);
        self::assertSame(38.0, $lastMeasurement->neck);
        self::assertSame(85.0, $lastMeasurement->waist);
        self::assertSame(100.0, $lastMeasurement->chest);
        self::assertSame(95.0, $lastMeasurement->hips);
    }

    public function testSave(): void
    {
        $measurement = new BodyMeasurement(age: 30, height: 175);
        $measurement->weight = 80.0;
        $measurement->neck = 38.0;
        $measurement->waist = 85.0;
        $measurement->chest = 100.0;

        $this->repository->save($measurement);

        self::clearManager();

        $found = $this->repository->findOneBy(['id' => $measurement->id]);

        self::assertNotNull($found);
        self::assertSame(80.0, $found->weight);
        self::assertSame(38.0, $found->neck);
        self::assertSame(85.0, $found->waist);
        self::assertSame(100.0, $found->chest);
    }

    public function testSaveUpdate(): void
    {
        $measurement = new BodyMeasurement(age: 30, height: 175);
        $measurement->weight = 80.0;

        $this->repository->save($measurement);

        self::clearManager();

        $found = $this->repository->findOneBy(['id' => $measurement->id]);
        self::assertNotNull($found);
        self::assertSame(80.0, $found->weight);

        $found->weight = 82.0;
        $this->repository->save($found);

        self::clearManager();

        $updated = $this->repository->findOneBy(['id' => $measurement->id]);
        self::assertNotNull($updated);
        self::assertSame(82.0, $updated->weight);
    }
}
