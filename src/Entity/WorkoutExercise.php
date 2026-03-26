<?php

declare(strict_types=1);

namespace Continuum\Entity;

use Continuum\Repository\WorkoutExerciseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: WorkoutExerciseRepository::class)]
#[ORM\Table(name: 'workout_exercises')]
final class WorkoutExercise
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    public private(set) Uuid $id;

    /**
     * @var Collection<int, WorkoutSet>
     */
    #[ORM\OneToMany(targetEntity: WorkoutSet::class, mappedBy: 'workoutExercise', orphanRemoval: true)]
    public private(set) Collection $sets;

    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'workoutExercises')]
        #[ORM\JoinColumn(nullable: false)]
        public readonly Workout $workout,
        #[ORM\ManyToOne(inversedBy: 'workoutExercises')]
        #[ORM\JoinColumn(nullable: false)]
        public readonly Exercise $exercise,
        #[ORM\Column(type: Types::SMALLINT)]
        public readonly int $orderIndex = 0,
    ) {
        $this->id = Uuid::v7();
        $this->sets = new ArrayCollection();
    }
}
