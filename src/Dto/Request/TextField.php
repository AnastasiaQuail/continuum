<?php

declare(strict_types=1);

namespace Continuum\Dto\Request;

use Continuum\Entity\TextField as TextFieldEntity;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class TextField
{
    public function __construct(
        #[Assert\NotBlank]
        public string $text,
        public bool $isPrivate,
    ) {}

    public static function create(TextFieldEntity $entity): self
    {
        return new self(
            text: $entity->text,
            isPrivate: $entity->isPrivate,
        );
    }
}
