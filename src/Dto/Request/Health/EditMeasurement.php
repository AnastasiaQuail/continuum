<?php

declare(strict_types=1);

namespace Continuum\Dto\Request\Health;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final readonly class EditMeasurement
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Range(min: 50, max: 100)]
        public int $weight,
        #[Assert\Range(min: 30, max: 50)]
        public ?int $neck = null,
        #[Assert\Range(min: 60, max: 120)]
        public ?int $waist = null,
    ) {}

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->neck !== null && $this->waist === null) {
            $context->buildViolation('This value is required if neck is filled')
                ->atPath('waist')
                ->addViolation();
        } elseif ($this->neck === null && $this->waist !== null) {
            $context->buildViolation('This value is required if waist is filled')
                ->atPath('neck')
                ->addViolation();
        }
    }
}
