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
    private Uuid $id;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $orderIndex = 0;

    /**
     * @var Collection<int, WorkoutSet>
     */
    #[ORM\OneToMany(targetEntity: WorkoutSet::class, mappedBy: 'WorkoutExercise', orphanRemoval: true)]
    private Collection $sets;

    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'workoutExercises')]
        #[ORM\JoinColumn(nullable: false)]
        private readonly Workout $workout,

        #[ORM\ManyToOne(inversedBy: 'workoutExercises')]
        #[ORM\JoinColumn(nullable: false)]
        private readonly Exercise $exercise,
    ) {
        $this->id = Uuid::v7();
        $this->sets = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getWorkout(): Workout
    {
        return $this->workout;
    }

    public function getExercise(): Exercise
    {
        return $this->exercise;
    }

    public function getOrderIndex(): int
    {
        return $this->orderIndex;
    }

    public function setOrderIndex(int $orderIndex): void
    {
        $this->orderIndex = $orderIndex;
    }

    /**
     * @return Collection<int, WorkoutSet>
     */
    public function getSets(): Collection
    {
        return $this->sets;
    }
}
