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
    public private(set) Uuid $id;

    #[ORM\Column]
    public private(set) DateTimeImmutable $performedAt;

    #[ORM\Column(name: 'weight')]
    private int $weightValue = 0;

    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'sets')]
        #[ORM\JoinColumn(nullable: false)]
        public readonly WorkoutExercise $workoutExercise,
        public float $weight {
            get => floor($this->weightValue / 100) / 10;
            set {
                $this->weightValue = (int) floor($value * 1000);
            }
        },
        #[ORM\Column]
        public readonly int $reps,
        #[ORM\Column]
        public readonly bool $isWarmup = false,
        #[ORM\Column(type: Types::SMALLINT)]
        public int $orderIndex = 0,
    ) {
        $this->id = Uuid::v7();
        $this->performedAt = new DateTimeImmutable();
    }
}
