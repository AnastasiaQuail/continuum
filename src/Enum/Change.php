<?php

declare(strict_types=1);

namespace Continuum\Enum;

enum Change: string
{
    case Unchanged = 'unchanged';
    case Increased = 'increased';
    case Decreased = 'decreased';

    public function isUnchanged(): bool
    {
        return self::Unchanged === $this;
    }
}
