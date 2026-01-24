<?php

declare(strict_types=1);

namespace Continuum\Entity;

use Continuum\Repository\WorkoutSetRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: WorkoutSetRepository::class)]
#[ORM\Table(name: 'workout_sets')]
final class WorkoutSet
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $orderIndex = 0;

    #[ORM\Column]
    private bool $isWarmup = false;

    #[ORM\Column]
    private DateTimeImmutable $performedAt;

    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'sets')]
        #[ORM\JoinColumn(nullable: false)]
        private readonly WorkoutExercise $WorkoutExercise,

        #[ORM\Column]
        private int $weight,

        #[ORM\Column]
        private int $reps,
    ) {
        $this->id = Uuid::v7();
        $this->performedAt = new DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getWorkoutExercise(): WorkoutExercise
    {
        return $this->WorkoutExercise;
    }

    public function getOrderIndex(): int
    {
        return $this->orderIndex;
    }

    public function setOrderIndex(int $orderIndex): void
    {
        $this->orderIndex = $orderIndex;
    }

    public function getWeight(): float
    {
        return round($this->weight / 1000, 1);
    }

    public function setWeight(int $weight): void
    {
        $this->weight = $weight;
    }

    public function getReps(): int
    {
        return $this->reps;
    }

    public function setReps(int $reps): void
    {
        $this->reps = $reps;
    }

    public function isWarmup(): bool
    {
        return $this->isWarmup;
    }

    public function setIsWarmup(bool $isWarmup): void
    {
        $this->isWarmup = $isWarmup;
    }

    public function getPerformedAt(): DateTimeImmutable
    {
        return $this->performedAt;
    }
}
