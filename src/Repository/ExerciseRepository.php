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
        return array_values($this->findBy([], ['group' => 'ASC']));
    }

    /**
     * @return array<string, int>
     */
    public function findWorkoutExerciseCountIndexedById(): array
    {
        /** @var array<string, array{id: Uuid, count: int}> $data */
        $data = $this->createQueryBuilder('e')
            ->select('e.id')
            ->addSelect('COUNT(we.id) AS count')
            ->leftJoin('e.workoutExercises', 'we')
            ->groupBy('e.id')
            ->indexBy('e', 'e.id')
            ->getQuery()
            ->execute();

        return array_map(
            static fn (array $row): int => $row['count'],
            $data
        );
    }

    public function save(Exercise $exercise): void
    {
        $this->getEntityManager()->persist($exercise);
        $this->getEntityManager()->flush();
    }
}
