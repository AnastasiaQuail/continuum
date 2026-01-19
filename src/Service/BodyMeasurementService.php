<?php

declare(strict_types=1);

namespace Continuum\Service;

use Continuum\Dto\Request\Health\EditBodyMeasurement;
use Continuum\Entity\BodyMeasurement;
use Continuum\Entity\User;
use Continuum\Repository\BodyMeasurementRepository;
use DateTimeImmutable;
use DateTimeZone;

final readonly class BodyMeasurementService
{
    public function __construct(
        private UserService $userService,
        private BodyMeasurementRepository $repository,
    ) {}

    /**
     * @return list<BodyMeasurement>
     */
    public function getByMonth(User $user, DateTimeImmutable $date): array
    {
        return $this->repository->findByMonth($date, $user->getTimezone());
    }

    public function getInitMeasurement(User $user, DateTimeImmutable $date): ?BodyMeasurement
    {
        $date = $date->modify('-1 month');

        return $this->repository->findOneLastByMonth($date, $user->getTimezone());
    }

    public function save(User $user, ?BodyMeasurement $measurement, EditBodyMeasurement $dto): BodyMeasurement
    {
        if ($measurement === null) {
            $measurement = new BodyMeasurement(
                age: $this->userService->getAge($user),
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
