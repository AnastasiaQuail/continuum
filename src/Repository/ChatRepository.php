<?php

declare(strict_types=1);

namespace Continuum\Repository;

use Continuum\Entity\Chat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Chat>
 */
final class ChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chat::class);
    }

    public function findOneById(Uuid $id, Uuid $userId): ?Chat
    {
        $result = $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->andWhere('c.user1 = :user_id OR c.user2 = :user_id')
            ->setParameter('user_id', $userId)
            ->getQuery()
            ->getOneOrNullResult();

        assert(null === $result || $result instanceof Chat);

        return $result;
    }

    public function findLastOneByUserId(Uuid $userId): ?Chat
    {
        $result = $this->createQueryBuilder('c')
            ->andWhere('c.user1 = :user_id OR c.user2 = :user_id')
            ->setParameter('user_id', $userId)
            ->addOrderBy('c.lastMessageAt', Order::Descending->value)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        assert(null === $result || $result instanceof Chat);

        return $result;
    }
}
