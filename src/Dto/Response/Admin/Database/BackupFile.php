<?php

declare(strict_types=1);

namespace Continuum\Dto\Response\Admin\Database;

use DateTimeImmutable;

final readonly class BackupFile
{
    public function __construct(
        public string $name,
        public int $size,
        public DateTimeImmutable $date,
    ) {}
}
