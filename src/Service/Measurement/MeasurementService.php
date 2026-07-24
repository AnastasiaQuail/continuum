<?php

declare(strict_types=1);

namespace Continuum\Service\Measurement;

use Continuum\Dto\Request\Measurement\EditMeasurement;
use Continuum\Dto\Response\Measurement\LastMeasurement;
use Continuum\Entity\BodyMeasurement;
use Continuum\Entity\User;
use Continuum\Repository\BodyMeasurementRepositoryInterface;
use Continuum\Service\GodUserService;
use DateTimeImmutable;
use DateTimeZone;

final readonly class MeasurementService
{
    public function __construct(
        private GodUserService $userService,
        private BodyMeasurementRepositoryInterface $repository,
    ) {}

    /**
     * @return list<BodyMeasurement>
     */
    public function getByMonth(User $user, DateTimeImmutable $month): array
    {
        $from = new DateTimeImmutable(
            sprintf('%s-%s-01 00:00:00', $month->format('Y'), $month->format('m')),
            $user->timezone
        );
        $to = new DateTimeImmutable(
            sprintf('%s-%s-%s 23:59:59', $month->format('Y'), $month->format('m'), $month->format('t')),
            $user->timezone
        );

        return $this->repository->findByRange($from, $to);
    }

    /**
     * @return list<BodyMeasurement>
     */
    public function getByRange(User $user, DateTimeImmutable $fromDay, DateTimeImmutable $toDay): array
    {
        $from = new DateTimeImmutable(
            sprintf('%s-%s-%s 00:00:00', $fromDay->format('Y'), $fromDay->format('m'), $fromDay->format('d')),
            $user->timezone
        );
        $to = new DateTimeImmutable(
            sprintf('%s-%s-%s 23:59:59', $toDay->format('Y'), $toDay->format('m'), $toDay->format('d')),
            $user->timezone
        );

        return $this->repository->findByRange($from, $to);
    }

    public function getLastMeasurement(): LastMeasurement
    {
        return $this->repository->findOneLastWithNotNull();
    }

    public function getInitMeasurement(User $user, DateTimeImmutable $date): ?BodyMeasurement
    {
        $date = $date->modify('-1 month');

        return $this->repository->findOneLastByMonth($date, $user->timezone);
    }

    public function save(User $user, EditMeasurement $dto, ?BodyMeasurement $measurement = null): BodyMeasurement
    {
        if (null === $measurement) {
            $measurement = new BodyMeasurement(
                age: $this->userService->getAge($user, $dto->datetime),
                height: $this->userService->getHeight(),
            );
        }

        $measurement->datetime = $dto->datetime->setTimezone(new DateTimeZone('UTC'));
        $measurement->weight = $dto->weight;
        $measurement->neck = $dto->neck;
        $measurement->chest = $dto->chest;
        $measurement->shoulders = $dto->shoulders;
        $measurement->waist = $dto->waist;
        $measurement->flexedBiceps = $dto->flexedBiceps;
        $measurement->hips = $dto->hips;
        $measurement->thigh = $dto->thigh;
        $measurement->calf = $dto->calf;

        $this->repository->save($measurement);

        return $measurement;
    }

    public function findPrevValue(BodyMeasurement $measurement, string $fieldName): ?float
    {
        return $this->repository->findPrevByFieldName($measurement, $fieldName);
    }

    public function delete(BodyMeasurement $measurement): void
    {
        $this->repository->delete($measurement);
    }
}
