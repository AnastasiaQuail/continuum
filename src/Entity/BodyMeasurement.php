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
    public float $fatDeurenberg = 0.0;

    #[ORM\Column(type: Types::SMALLFLOAT, nullable: true)]
    public ?float $fatUsNavy = null;

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
        private readonly int $age,
        #[ORM\Column]
        private readonly int $height,
    ) {
        $this->id = Uuid::v7();
        $this->datetime = new DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getDatetime(): DateTimeImmutable
    {
        return $this->datetime;
    }

    public function setDatetime(DateTimeImmutable $datetime): void
    {
        $this->datetime = $datetime;
    }

    public function getFatUsNavy(): ?float
    {
        return $this->fatUsNavy;
    }

    public function getFatDeurenberg(): float
    {
        return $this->fatDeurenberg;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function calculateFat(): void
    {
        /*$bmi = $this->getWeight() / (($this->height / 100) ** 2);
        $fatDeurenberg = (1.2 * $bmi) + (0.23 * $this->age) - 16.2;

        $this->fatDeurenberg = round($fatDeurenberg, 2);

        if (
            (null !== $waist = $this->getWaist())
            && (null !== $neck = $this->getNeck())
        ) {
            $logBody = log10($waist - $neck);
            $logHeight = log10($this->height);
            $fatUsNavy = (495 / (1.0324 - (0.19077 * $logBody) + (0.15456 * $logHeight))) - 450;

            $this->fatUsNavy = round($fatUsNavy, 2);
        }*/
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): void
    {
        $this->weight = $weight;
    }

    public function getNeck(): ?float
    {
        return $this->neck;
    }

    public function setNeck(?float $neck): void
    {
        $this->neck = $neck;
    }

    public function getChest(): ?float
    {
        return $this->chest;
    }

    public function setChest(?float $chest): void
    {
        $this->chest = $chest;
    }

    public function getShoulders(): ?float
    {
        return $this->shoulders;
    }

    public function setShoulders(?float $shoulders): void
    {
        $this->shoulders = $shoulders;
    }

    public function getWaist(): ?float
    {
        return $this->waist;
    }

    public function setWaist(?float $waist): void
    {
        $this->waist = $waist;
    }

    public function getFlexedBiceps(): ?float
    {
        return $this->flexedBiceps;
    }

    public function setFlexedBiceps(?float $flexedBiceps): void
    {
        $this->flexedBiceps = $flexedBiceps;
    }

    public function getHips(): ?float
    {
        return $this->hips;
    }

    public function setHips(?float $hips): void
    {
        $this->hips = $hips;
    }

    public function getThigh(): ?float
    {
        return $this->thigh;
    }

    public function setThigh(?float $thigh): void
    {
        $this->thigh = $thigh;
    }

    public function getCalf(): ?float
    {
        return $this->calf;
    }

    public function setCalf(?float $calf): void
    {
        $this->calf = $calf;
    }
}
