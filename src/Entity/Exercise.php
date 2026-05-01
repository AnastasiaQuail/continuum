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
    public private(set) Uuid $id;

    /**
     * @var Collection<int, WorkoutExercise>
     */
    #[ORM\OneToMany(targetEntity: WorkoutExercise::class, mappedBy: 'exercise', orphanRemoval: true)]
    public private(set) Collection $workoutExercises;

    public function __construct(
        #[ORM\Column(length: 255)]
        public string $name {
            set => mb_ucfirst(mb_strtolower($value));
        },
        #[ORM\Column(name: 'exercise_group', enumType: ExerciseGroup::class)]
        public ExerciseGroup $group,
        #[ORM\Column(options: ['default' => true])]
        public bool $isActive = true,
    ) {
        $this->id = Uuid::v7();
        $this->workoutExercises = new ArrayCollection();
    }
}
