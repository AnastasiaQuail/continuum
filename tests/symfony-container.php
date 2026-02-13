<?php

declare(strict_types=1);

use Continuum\Kernel;
use Symfony\Component\Dotenv\Dotenv;

/**
 * @var non-empty-array<non-empty-string, mixed> $_SERVER
 */

require dirname(__DIR__) . '/vendor/autoload.php';

new Dotenv()->bootEnv(dirname(__DIR__) . '/.env');

/** @var Closure(non-empty-array<string, mixed> $context): Kernel $closure */
$closure = include dirname(__DIR__) . '/public/index.php';

$kernel = $closure($_SERVER);
$kernel->boot();

return $kernel->getContainer();
