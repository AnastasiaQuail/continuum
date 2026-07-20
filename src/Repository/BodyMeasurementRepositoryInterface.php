<?php

declare(strict_types=1);

namespace Continuum\Repository;

use Continuum\Dto\Response\Measurement\LastMeasurement;
use Continuum\Entity\BodyMeasurement;
use DateTimeImmutable;
use DateTimeZone;

interface BodyMeasurementRepositoryInterface
{
    /**
     * @return list<BodyMeasurement>
     */
    public function findByRange(DateTimeImmutable $from, DateTimeImmutable $to): array;

    public function findOneLastByMonth(DateTimeImmutable $month, DateTimeZone $timeZone): ?BodyMeasurement;

    public function findOneLastWithNotNull(): LastMeasurement;

    public function findPrevByFieldName(BodyMeasurement $measurement, string $fieldName): ?float;

    public function save(BodyMeasurement $measurement): void;
}
