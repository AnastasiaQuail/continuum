<?php

declare(strict_types=1);

namespace Continuum\Security\Attribute;

use Attribute;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Attribute(Attribute::TARGET_METHOD)]
final class IsFutureMonthGranted extends IsGranted
{
    public const string ATTRIBUTE = 'FUTURE_MONTH';

    public function __construct(string $subject)
    {
        parent::__construct(self::ATTRIBUTE, $subject);
    }
}
