<?php

declare(strict_types=1);

namespace Continuum\Entity;

use Continuum\Repository\WorkoutRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: WorkoutRepository::class)]
#[ORM\Table(name: 'workouts')]
final class Workout
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    public private(set) Uuid $id;

    #[ORM\Column]
    public private(set) DateTimeImmutable $date;

    /**
     * @var Collection<int, WorkoutExercise>
     */
    #[ORM\OneToMany(targetEntity: WorkoutExercise::class, mappedBy: 'workout', orphanRemoval: true)]
    public private(set) Collection $workoutExercises;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->date = new DateTimeImmutable();
        $this->workoutExercises = new ArrayCollection();
    }
}
