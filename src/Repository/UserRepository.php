<?php

declare(strict_types=1);

namespace Continuum\Repository;

use Continuum\Entity\Exercise;
use Continuum\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
final class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
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

        $user->setPassword($newHashedPassword);

        $this->save($user);
    }

    public function updateLastVisitedAt(User $user): void
    {
        $this->createQueryBuilder('u')
            ->update()
            ->andWhere('u.id = :id')
            ->set('u.lastVisitedAt', ':datetime')
            ->setParameter('id', $user->getId())
            ->setParameter(':datetime', new DateTimeImmutable())
            ->getQuery()
            ->execute();
    }

    /**
     * @return list<User>
     */
    public function findOrdered(): array
    {
        return $this->findBy([], ['createdAt' => 'ASC']);
    }

    public function save(User $user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}
