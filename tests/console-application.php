<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Dotenv\Dotenv;

/**
 * @var non-empty-array<non-empty-string, mixed> $_SERVER
 */

require dirname(__DIR__) . '/vendor/autoload.php';

new Dotenv()->bootEnv(dirname(__DIR__) . '/.env');

/** @var Closure(non-empty-array<string, mixed> $context): Application $closure */
$closure = include dirname(__DIR__) . '/bin/console';

return $closure($_SERVER);
