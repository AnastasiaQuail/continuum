<?php

declare(strict_types=1);

namespace Continuum\Service;

use Continuum\Dto\Request\Admin\User\EditUser;
use Continuum\Dto\Request\User\EditLocation;
use Continuum\Entity\Location;
use Continuum\Entity\User;
use Continuum\Repository\UserRepository;
use DateTimeZone;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

final readonly class UserService
{
    public function __construct(
        private UserRepository $repository,
    ) {}

    public function get(Uuid $id): User
    {
        return $this->repository->findOneById($id) ?? throw new NotFoundHttpException('User not found');
    }

    /**
     * @return list<User>
     */
    public function getAll(): array
    {
        return $this->repository->findOrdered();
    }

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

    public function update(User $user, EditUser $dto): User
    {
        $user->setStatus($dto->status);

        $this->repository->save($user);
        $this->repository->saveRoles($user, ...$dto->roles);

        return $user;
    }
}
