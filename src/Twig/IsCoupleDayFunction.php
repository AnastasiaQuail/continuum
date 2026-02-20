<?php

declare(strict_types=1);

namespace Continuum\Twig;

use Continuum\Service\CoupleService;
use Twig\Attribute\AsTwigFunction;

final readonly class IsCoupleDayFunction
{
    public function __construct(
        private CoupleService $coupleService,
    ) {}

    #[AsTwigFunction('is_couple_day')]
    public function __invoke(): bool
    {
        return $this->coupleService->getTogether()->isStartDay();
    }
}
