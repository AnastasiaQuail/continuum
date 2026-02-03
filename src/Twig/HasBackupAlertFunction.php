<?php

declare(strict_types=1);

namespace Continuum\Twig;

use Continuum\Service\Database\DatabaseDumpCache;
use Twig\Attribute\AsTwigFunction;

final readonly class HasBackupAlertFunction
{
    public function __construct(
        private DatabaseDumpCache $databaseDumpCache,
    ) {}

    #[AsTwigFunction('has_backup_alert')]
    public function __invoke(): bool
    {
        return false === $this->databaseDumpCache->has();
    }
}
