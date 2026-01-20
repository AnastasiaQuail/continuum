<?php

declare(strict_types=1);

namespace Continuum\Security\Attribute;

use Attribute;
use Continuum\Security\Authorization\Voter\FutureMonthVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Attribute(Attribute::TARGET_METHOD)]
final class IsFutureMonthGranted extends IsGranted
{
    public function __construct(string $subject)
    {
        parent::__construct(FutureMonthVoter::ATTRIBUTE, $subject);
    }
}
