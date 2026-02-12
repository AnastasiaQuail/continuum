<?php

declare(strict_types=1);

namespace Continuum\Dto\Request\Measurement;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final readonly class EditMeasurement
{
    public const int WEIGHT_MIN = 50;
    public const int WEIGHT_MAX = 100;
    public const int NECK_MIN = 30;
    public const int NECK_MAX = 50;
    public const int CHEST_MIN = 60;
    public const int CHEST_MAX = 150;
    public const int SHOULDERS_MIN = 60;
    public const int SHOULDERS_MAX = 150;
    public const int WAIST_MIN = 60;
    public const int WAIST_MAX = 150;
    public const int BICEPS_MIN = 20;
    public const int BICEPS_MAX = 60;
    public const int HIPS_MIN = 60;
    public const int HIPS_MAX = 150;
    public const int THIGH_MIN = 30;
    public const int THIGH_MAX = 100;
    public const int CALF_MIN = 20;
    public const int CALF_MAX = 60;

    public function __construct(
        public DateTimeImmutable $datetime,
        #[Assert\NotBlank]
        #[Assert\Range(min: self::WEIGHT_MIN, max: self::WEIGHT_MAX)]
        public float $weight,
        #[Assert\Range(min: self::NECK_MIN, max: self::NECK_MAX)]
        public ?float $neck = null,
        #[Assert\Range(min: self::CHEST_MIN, max: self::CHEST_MAX)]
        public ?float $chest = null,
        #[Assert\Range(min: self::SHOULDERS_MIN, max: self::SHOULDERS_MAX)]
        public ?float $shoulders = null,
        #[Assert\Range(min: self::WAIST_MIN, max: self::WAIST_MAX)]
        public ?float $waist = null,
        #[Assert\Range(min: self::BICEPS_MIN, max: self::BICEPS_MAX)]
        public ?float $flexedBiceps = null,
        #[Assert\Range(min: self::HIPS_MIN, max: self::HIPS_MAX)]
        public ?float $hips = null,
        #[Assert\Range(min: self::THIGH_MIN, max: self::THIGH_MAX)]
        public ?float $thigh = null,
        #[Assert\Range(min: self::CALF_MIN, max: self::CALF_MAX)]
        public ?float $calf = null,
    ) {}

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if (null !== $this->neck && null === $this->waist) {
            $context->buildViolation('This value is required if neck is filled')
                ->atPath('waist')
                ->addViolation();
        } elseif (null === $this->neck && null !== $this->waist) {
            $context->buildViolation('This value is required if waist is filled')
                ->atPath('neck')
                ->addViolation();
        }
    }
}
