<?php

declare(strict_types=1);

use Continuum\Kernel;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

/** @var non-empty-array<non-empty-string, mixed> $_SERVER */
new Dotenv()->bootEnv(dirname(__DIR__) . '/.env');

/** @var Callable(non-empty-array<string, mixed> $context): Kernel $closure */
$closure = include dirname(__DIR__) . '/public/index.php';

$kernel = $closure($_SERVER);
$kernel->boot();

return $kernel->getContainer();
