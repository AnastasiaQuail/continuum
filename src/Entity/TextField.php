<?php

declare(strict_types=1);

namespace Continuum\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Embeddable]
final class TextField
{
    public function __construct(
        #[ORM\Column(type: Types::TEXT)]
        public private(set) string $text {
            set => '' !== $value ? $value : throw new InvalidArgumentException('Text cannot be empty.');
        },
        #[ORM\Column(type: Types::BOOLEAN)]
        public readonly bool $isPrivate,
    ) {}
}
