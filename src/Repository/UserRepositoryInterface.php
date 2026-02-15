<?php

declare(strict_types=1);

namespace Continuum\Repository;

use Continuum\Entity\User;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

interface UserRepositoryInterface
{
    public function findOneById(Uuid $id): ?User;

    /**
     * @return list<User>
     */
    public function findOrdered(): array;

    public function save(User $user): void;

    /**
     * @param non-empty-string ...$roles
     */
    public function updateRoles(Uuid $id, string ...$roles): void;

    public function updateLastVisitedAt(Uuid $id, DateTimeImmutable $lastVisitedAt): void;
}
