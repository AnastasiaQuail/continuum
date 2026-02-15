<?php

declare(strict_types=1);

namespace Continuum\Repository;

use Continuum\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<User>
 */
final class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        try {
            $user->password = $newHashedPassword;
        } catch (InvalidArgumentException $exception) {
            throw new UnsupportedUserException('New password cannot be blank.', $exception->getCode(), $exception);
        }

        $this->save($user);
    }

    public function findOneById(Uuid $id): ?User
    {
        return $this->find($id);
    }

    /**
     * @return list<User>
     */
    public function findOrdered(): array
    {
        return array_values($this->findBy([], ['createdAt' => 'ASC']));
    }

    public function save(User $user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @param non-empty-string ...$roles
     */
    public function updateRoles(Uuid $id, string ...$roles): void
    {
        $this->createQueryBuilder('u')
            ->update()
            ->set('u.roles', ':roles')
            ->andWhere('u.id = :id')
            ->setParameter('id', $id)
            ->setParameter(':roles', $roles, Types::JSON)
            ->getQuery()
            ->execute();
    }

    public function updateLastVisitedAt(Uuid $id, DateTimeImmutable $lastVisitedAt): void
    {
        $this->createQueryBuilder('u')
            ->update()
            ->andWhere('u.id = :id')
            ->set('u.lastVisitedAt', ':datetime')
            ->setParameter('id', $id)
            ->setParameter(':datetime', $lastVisitedAt)
            ->getQuery()
            ->execute();
    }
}
