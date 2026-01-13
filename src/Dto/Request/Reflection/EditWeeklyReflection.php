<?php

declare(strict_types=1);

namespace Continuum\Dto\Request\Reflection;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class EditWeeklyReflection
{
    public function __construct(
        #[Assert\NotBlank]
        public string $joy,
        public bool $isJoyPrivate,
        #[Assert\NotBlank]
        public string $difficulty,
        public bool $isDifficultyPrivate,
        #[Assert\NotBlank]
        public string $achievement,
        public bool $isAchievementPrivate,
    ) {}
}
