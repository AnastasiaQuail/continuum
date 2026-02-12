<?php

declare(strict_types=1);

namespace Continuum\Entity;

use Continuum\Enum\ExerciseGroup;
use Continuum\Repository\ExerciseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ExerciseRepository::class)]
#[ORM\Table(name: 'exercises')]
final class Exercise
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    /**
     * @var Collection<int, WorkoutExercise>
     */
    #[ORM\OneToMany(targetEntity: WorkoutExercise::class, mappedBy: 'exercise', orphanRemoval: true)]
    private Collection $workoutExercises;

    public function __construct(
        #[ORM\Column(length: 255)]
        private string $name,
        #[ORM\Column(name: 'exercise_group', enumType: ExerciseGroup::class)]
        private ExerciseGroup $group,
    ) {
        $this->id = Uuid::v7();
        $this->workoutExercises = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getGroup(): ExerciseGroup
    {
        return $this->group;
    }

    public function setGroup(ExerciseGroup $group): void
    {
        $this->group = $group;
    }

    /**
     * @return Collection<int, WorkoutExercise>
     */
    public function getWorkoutExercises(): Collection
    {
        return $this->workoutExercises;
    }
}
