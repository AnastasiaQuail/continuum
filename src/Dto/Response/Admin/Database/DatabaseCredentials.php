<?php

declare(strict_types=1);

namespace Continuum\Dto\Response\Admin\Database;

use SensitiveParameter;

final readonly class DatabaseCredentials
{
    public function __construct(
        public string $user,
        #[SensitiveParameter]
        public string $password,
        public string $host,
        public int $port,
        public string $name,
    ) {}
}
