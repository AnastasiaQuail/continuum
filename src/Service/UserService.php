<?php

declare(strict_types=1);

namespace Continuum\Service;

use Continuum\Dto\Request\Admin\User\EditUser;
use Continuum\Dto\Request\User\EditLocation;
use Continuum\Entity\Location;
use Continuum\Entity\User;
use Continuum\Repository\UserRepositoryInterface;
use DateTimeZone;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

final readonly class UserService
{
    public function __construct(
        private UserRepositoryInterface $repository,
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
        $user->timezone = $timezone;

        $this->repository->save($user);
    }

    public function updateLocation(User $user, EditLocation $dto): void
    {
        $user->location = new Location(
            latitude: $dto->latitude,
            longitude: $dto->longitude,
        );

        $this->repository->save($user);
    }

    public function update(User $user, EditUser $dto): User
    {
        $user->status = $dto->status;

        $this->repository->save($user);
        $this->repository->updateRoles($user->id, ...$dto->roles);

        return $user;
    }
}
