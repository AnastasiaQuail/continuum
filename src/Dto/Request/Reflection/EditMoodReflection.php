<?php

declare(strict_types=1);

namespace Continuum\Dto\Request\Reflection;

use Continuum\Enum\MoodType;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class EditMoodReflection
{
    public function __construct(
        public MoodType $type,
        #[Assert\Length(max: 255)]
        public string $text,
    ) {}
}
