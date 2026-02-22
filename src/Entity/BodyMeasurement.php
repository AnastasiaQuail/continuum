<?php

declare(strict_types=1);

namespace Continuum\Entity;

use Continuum\Repository\BodyMeasurementRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: BodyMeasurementRepository::class)]
#[ORM\Table(name: 'body_measurements')]
#[ORM\HasLifecycleCallbacks]
final class BodyMeasurement
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    public private(set) Uuid $id;

    #[ORM\Column]
    public DateTimeImmutable $datetime;

    #[ORM\Column(type: Types::SMALLFLOAT)]
    public private(set) float $fatDeurenberg = 0.0;

    #[ORM\Column(type: Types::SMALLFLOAT, nullable: true)]
    public private(set) ?float $fatUsNavy = null;

    #[ORM\Column(type: Types::SMALLFLOAT)]
    public float $weight = 0.0;

    #[ORM\Column(type: Types::SMALLFLOAT, nullable: true)]
    public ?float $neck = null;

    #[ORM\Column(type: Types::SMALLFLOAT, nullable: true)]
    public ?float $chest = null;

    #[ORM\Column(type: Types::SMALLFLOAT, nullable: true)]
    public ?float $shoulders = null;

    #[ORM\Column(type: Types::SMALLFLOAT, nullable: true)]
    public ?float $waist = null;

    #[ORM\Column(type: Types::SMALLFLOAT, nullable: true)]
    public ?float $flexedBiceps = null;

    #[ORM\Column(type: Types::SMALLFLOAT, nullable: true)]
    public ?float $hips = null;

    #[ORM\Column(type: Types::SMALLFLOAT, nullable: true)]
    public ?float $thigh = null;

    #[ORM\Column(type: Types::SMALLFLOAT, nullable: true)]
    public ?float $calf = null;

    public function __construct(
        #[ORM\Column]
        public readonly int $age,
        #[ORM\Column]
        public readonly int $height,
    ) {
        $this->id = Uuid::v7();
        $this->datetime = new DateTimeImmutable();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function calculateFat(): void
    {
        $bmi = $this->weight / (($this->height / 100) ** 2);
        $fatDeurenberg = (1.2 * $bmi) + (0.23 * $this->age) - 16.2;

        $this->fatDeurenberg = round($fatDeurenberg, 2);

        if (
            (null !== $waist = $this->waist)
            && (null !== $neck = $this->neck)
        ) {
            $logBody = log10($waist - $neck);
            $logHeight = log10($this->height);
            $fatUsNavy = (495 / (1.0324 - (0.19077 * $logBody) + (0.15456 * $logHeight))) - 450;

            $this->fatUsNavy = round($fatUsNavy, 2);
        }
    }
}
