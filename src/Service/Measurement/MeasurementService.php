<?php

declare(strict_types=1);

namespace Continuum\Service\Measurement;

use Continuum\Dto\Request\Measurement\EditMeasurement;
use Continuum\Dto\Response\Measurement\LastMeasurement;
use Continuum\Entity\BodyMeasurement;
use Continuum\Entity\User;
use Continuum\Repository\BodyMeasurementRepository;
use Continuum\Service\GodUserService;
use DateTimeImmutable;
use DateTimeZone;

final readonly class MeasurementService
{
    public function __construct(
        private GodUserService $userService,
        private BodyMeasurementRepository $repository,
    ) {}

    /**
     * @return list<BodyMeasurement>
     */
    public function getByMonth(User $user, DateTimeImmutable $month): array
    {
        $from = new DateTimeImmutable(
            sprintf('%d-%d-01 00:00:00', $month->format('Y'), $month->format('m')),
            $user->getTimezone()
        );
        $to = new DateTimeImmutable(
            sprintf('%d-%d-%d 23:59:59', $month->format('Y'), $month->format('m'), $month->format('t')),
            $user->getTimezone()
        );

        return $this->repository->findByRange($from, $to);
    }

    /**
     * @return list<BodyMeasurement>
     */
    public function getByRange(User $user, DateTimeImmutable $fromDay, DateTimeImmutable $toDay): array
    {
        $from = new DateTimeImmutable(
            sprintf('%d-%d-%d 00:00:00', $fromDay->format('Y'), $fromDay->format('m'), $fromDay->format('d')),
            $user->getTimezone()
        );
        $to = new DateTimeImmutable(
            sprintf('%d-%d-%d 23:59:59', $toDay->format('Y'), $toDay->format('m'), $toDay->format('d')),
            $user->getTimezone()
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

        return $this->repository->findOneLastByMonth($date, $user->getTimezone());
    }

    public function save(User $user, ?BodyMeasurement $measurement, EditMeasurement $dto): BodyMeasurement
    {
        if (null === $measurement) {
            $measurement = new BodyMeasurement(
                age: $this->userService->getAge($user, $dto->datetime),
                height: $this->userService->getHeight(),
            );
        }

        $measurement->setDatetime($dto->datetime->setTimezone(new DateTimeZone('UTC')));
        $measurement->setWeight($dto->weight);
        $measurement->setNeck($dto->neck);
        $measurement->setChest($dto->chest);
        $measurement->setShoulders($dto->shoulders);
        $measurement->setWaist($dto->waist);
        $measurement->setFlexedBiceps($dto->flexedBiceps);
        $measurement->setHips($dto->hips);
        $measurement->setThigh($dto->thigh);
        $measurement->setCalf($dto->calf);

        $this->repository->save($measurement);

        return $measurement;
    }
}
