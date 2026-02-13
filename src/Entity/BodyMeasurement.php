<?php

declare(strict_types=1);

namespace Continuum\Entity;

use Continuum\Repository\BodyMeasurementRepository;
use DateTimeImmutable;
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
    private Uuid $id;

    #[ORM\Column]
    private DateTimeImmutable $datetime;

    #[ORM\Column]
    private int $fatDeurenberg = 0;

    #[ORM\Column(nullable: true)]
    private ?int $fatUsNavy = null;

    #[ORM\Column]
    private int $weight = 0;

    #[ORM\Column(nullable: true)]
    private ?int $neck = null;

    #[ORM\Column(nullable: true)]
    private ?int $chest = null;

    #[ORM\Column(nullable: true)]
    private ?int $shoulders = null;

    #[ORM\Column(nullable: true)]
    private ?int $waist = null;

    #[ORM\Column(nullable: true)]
    private ?int $flexedBiceps = null;

    #[ORM\Column(nullable: true)]
    private ?int $hips = null;

    #[ORM\Column(nullable: true)]
    private ?int $thigh = null;

    #[ORM\Column(nullable: true)]
    private ?int $calf = null;

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
        return $this->from($this->fatUsNavy, coefficient: 100, precision: 2);
    }

    public function getFatDeurenberg(): float
    {
        return $this->from($this->fatDeurenberg, coefficient: 100, precision: 2);
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function calculateFat(): void
    {
        $bmi = $this->getWeight() / (($this->height / 100) ** 2);
        $fatDeurenberg = (1.2 * $bmi) + (0.23 * $this->age) - 16.2;

        $this->fatDeurenberg = (int) round($fatDeurenberg * 100);

        if (
            (null !== $waist = $this->getWaist())
            && (null !== $neck = $this->getNeck())
        ) {
            $logBody = log10($waist - $neck);
            $logHeight = log10($this->height);
            $fatUsNavy = (495 / (1.0324 - (0.19077 * $logBody) + (0.15456 * $logHeight))) - 450;

            $this->fatUsNavy = (int) round($fatUsNavy * 100);
        }
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
        return $this->from($this->weight, coefficient: 1000);
    }

    public function setWeight(float $weight): void
    {
        $this->weight = $this->to($weight, coefficient: 1000);
    }

    public function getNeck(): ?float
    {
        return $this->from($this->neck);
    }

    public function setNeck(?float $neck): void
    {
        $this->neck = $this->to($neck);
    }

    public function getChest(): ?float
    {
        return $this->from($this->chest);
    }

    public function setChest(?float $chest): void
    {
        $this->chest = $this->to($chest);
    }

    public function getShoulders(): ?float
    {
        return $this->from($this->shoulders);
    }

    public function setShoulders(?float $shoulders): void
    {
        $this->shoulders = $this->to($shoulders);
    }

    public function getWaist(): ?float
    {
        return $this->from($this->waist);
    }

    public function setWaist(?float $waist): void
    {
        $this->waist = $this->to($waist);
    }

    public function getFlexedBiceps(): ?float
    {
        return $this->from($this->flexedBiceps);
    }

    public function setFlexedBiceps(?float $flexedBiceps): void
    {
        $this->flexedBiceps = $this->to($flexedBiceps);
    }

    public function getHips(): ?float
    {
        return $this->from($this->hips);
    }

    public function setHips(?float $hips): void
    {
        $this->hips = $this->to($hips);
    }

    public function getThigh(): ?float
    {
        return $this->from($this->thigh);
    }

    public function setThigh(?float $thigh): void
    {
        $this->thigh = $this->to($thigh);
    }

    public function getCalf(): ?float
    {
        return $this->from($this->calf);
    }

    public function setCalf(?float $calf): void
    {
        $this->calf = $this->to($calf);
    }

    /**
     * @template T of int|null
     *
     * @param T $value
     *
     * @return (T is int ? float : null)
     */
    private function from(?int $value, int $coefficient = 10, int $precision = 1): ?float
    {
        return null !== $value ? round($value / $coefficient, $precision) : null;
    }

    /**
     * @template T of float|null
     *
     * @param T $value
     *
     * @return (T is float ? int : null)
     */
    private function to(?float $value, int $coefficient = 10): ?int
    {
        return null !== $value ? (int) round($value * $coefficient) : null;
    }
}
