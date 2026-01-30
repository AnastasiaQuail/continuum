<?php

declare(strict_types=1);

namespace Continuum\Repository;

use Continuum\Entity\Exercise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Exercise>
 */
final class ExerciseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exercise::class);
    }

    public function findOneById(Uuid $id): ?Exercise
    {
        return $this->find($id);
    }

    /**
     * @return list<Exercise>
     */
    public function findOrdered(): array
    {
        return $this->findBy([], ['group' => 'ASC']);
    }

    public function save(Exercise $exercise): void
    {
        $this->getEntityManager()->persist($exercise);
        $this->getEntityManager()->flush();
    }
}
