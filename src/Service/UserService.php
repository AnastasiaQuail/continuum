<?php

declare(strict_types=1);

namespace Continuum\Service;

use Continuum\Dto\Request\User\EditLocation;
use Continuum\Entity\Location;
use Continuum\Entity\User;
use Continuum\Repository\UserRepository;
use DateTimeZone;

final readonly class UserService
{
    public function __construct(
        private UserRepository $repository,
    ) {}

    public function updateTimezone(User $user, DateTimeZone $timezone): void
    {
        $user->setTimezone($timezone);

        $this->repository->save($user);
    }

    public function updateLocation(User $user, EditLocation $dto): void
    {
        $user->setLocation(
            new Location(
                latitude: (string) $dto->latitude,
                longitude: (string) $dto->longitude,
            )
        );

        $this->repository->save($user);
    }
}
