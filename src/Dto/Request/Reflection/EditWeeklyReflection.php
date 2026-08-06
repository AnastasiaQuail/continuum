<?php

declare(strict_types=1);

namespace Continuum\Dto\Request\Reflection;

use Continuum\Dto\Request\TextField;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class EditWeeklyReflection
{
    public function __construct(
        #[Assert\Valid]
        public TextField $joy,
        #[Assert\Valid]
        public TextField $difficulty,
        #[Assert\Valid]
        public TextField $achievement,
    ) {}
}
