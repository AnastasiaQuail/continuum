<?php

declare(strict_types=1);

namespace Continuum\Service\Measurement;

use Continuum\Dto\Response\Measurement\ChartMeasurement;
use Continuum\Entity\BodyMeasurement;
use Continuum\Entity\User;
use DateTimeImmutable;

final readonly class ChartMeasurementService
{
    public function __construct(
        private MeasurementService $measurementService,
    ) {}

    /**
     * @param list<BodyMeasurement> $measurements
     *
     * @return non-empty-list<ChartMeasurement>
     */
    public function getChartMeasurements(User $user, DateTimeImmutable $month, array $measurements): array
    {
        if ($measurements === []) {
            return [];
        }

        $initMeasurement = $this->measurementService->getInitMeasurement($user, $month);
        /** @var BodyMeasurement $initMeasurement */
        $initMeasurement ??= array_first($measurements);

        $chartMeasurements = [
            $prevChartMeasurement = ChartMeasurement::first(
                $initMeasurement->getFatDeurenberg(),
                $initMeasurement->getWeight()
            ),
        ];

        foreach ($measurements as $measurement) {
            $chartMeasurements[] = $prevChartMeasurement = new ChartMeasurement(
                type: match (true) {
                    $prevChartMeasurement->fat < $measurement->getFatDeurenberg() => ChartMeasurement::TYPE_INCREASE,
                    $prevChartMeasurement->fat > $measurement->getFatDeurenberg() => ChartMeasurement::TYPE_DECREASE,
                    default => ChartMeasurement::TYPE_UNCHANGED,
                },
                prevTime: $prevChartMeasurement->time,
                time: $measurement->getDatetime()->getTimestamp() - $month->getTimestamp(),
                fat: $measurement->getFatDeurenberg(),
                weight: $measurement->getWeight(),
            );
        }

        return $chartMeasurements;
    }
}
