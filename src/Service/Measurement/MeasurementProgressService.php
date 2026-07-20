<?php

declare(strict_types=1);

namespace Continuum\Service\Measurement;

use Continuum\Dto\Response\Measurement\MeasurementFieldProgress;
use Continuum\Entity\BodyMeasurement;
use Continuum\Enum\Change;

final readonly class MeasurementProgressService
{
    public function __construct(
        private MeasurementService $measurementService,
    ) {}

    /**
     * @param list<BodyMeasurement> $measurements
     *
     * @return array<non-empty-string, array<non-empty-string, null|MeasurementFieldProgress>>
     */
    public function getProgresses(array $measurements): array
    {
        $fieldNames = BodyMeasurement::getMeasurementNames();
        $reversedFieldNames = BodyMeasurement::getProgressReversedMeasurementNames();

        /** @var array<non-empty-string, array<non-empty-string, MeasurementFieldProgress>> $progresses */
        $progresses = [];

        /** @var array<non-empty-string, float> $prevValues */
        $prevValues = [];

        /** @var array<non-empty-string, BodyMeasurement> $unhandledMeasurements */
        $unhandledMeasurements = [];

        foreach ($measurements as $measurement) {
            $id = (string) $measurement->id;

            foreach ($fieldNames as $fieldName) {
                $progresses[$id][$fieldName] = null;

                if (null === $value = $this->getMeasurement($measurement, $fieldName)) {
                    continue;
                }

                if (isset($prevValues[$fieldName])) {
                    $progresses[$id][$fieldName] = $this->getProgress(
                        $value,
                        $prevValues[$fieldName],
                        in_array($fieldName, $reversedFieldNames, true),
                    );
                } else {
                    $unhandledMeasurements[$fieldName] = $measurement;
                }

                $prevValues[$fieldName] = $value;
            }
        }

        foreach ($unhandledMeasurements as $fieldName => $measurement) {
            $prevValue = $this->measurementService->findPrevValue($measurement, $fieldName);

            if (null !== $prevValue) {
                $id = (string) $measurement->id;
                $progresses[$id][$fieldName] = $this->getProgress(
                    (float) $this->getMeasurement($measurement, $fieldName),
                    $prevValue,
                    in_array($fieldName, $reversedFieldNames, true),
                );
            }
        }

        return $progresses;
    }

    private function getMeasurement(BodyMeasurement $measurement, string $fieldName): ?float
    {
        // @phpstan-ignore return.type (always return null or float), property.dynamicName (the lesser of two evils)
        return $measurement->{$fieldName};
    }

    private function getProgress(float $value, float $prevValue, bool $isProgressReversed): MeasurementFieldProgress
    {
        return new MeasurementFieldProgress(
            value: round($value - $prevValue, 2),
            progress: match (true) {
                $prevValue < $value => Change::Increased,
                $prevValue > $value => Change::Decreased,
                default => Change::Unchanged,
            },
            isProgressReversed: $isProgressReversed,
        );
    }
}
