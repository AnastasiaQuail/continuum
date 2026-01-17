<?php

declare(strict_types=1);

namespace Continuum\Service;

use Continuum\Dto\Request\Health\EditBodyMeasurement;
use Continuum\Entity\BodyMeasurement;
use Continuum\Entity\User;
use Continuum\Repository\BodyMeasurementRepository;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class BodyMeasurementService
{
    public function __construct(
        #[Autowire(env: 'APP_USER_BIRTH_DATE')]
        private string $userBirthDate,
        #[Autowire(env: 'int:APP_USER_HEIGHT')]
        private int $userHeight,
        private BodyMeasurementRepository $repository,
    ) {}

    public function save(User $user, ?BodyMeasurement $measurement, EditBodyMeasurement $dto): BodyMeasurement
    {
        if ($measurement === null) {
            $birthday = new DateTimeImmutable($this->userBirthDate, $user->getTimezone())->setTime(0, 0);
            $now = new DateTimeImmutable('now', $user->getTimezone());

            $measurement = new BodyMeasurement(
                age: $birthday->diff($now)->y,
                height: $this->userHeight,
            );
        }

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
