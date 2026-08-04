<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

new Dotenv()->bootEnv(dirname(__DIR__) . '/.env');

/** @var Closure(non-empty-array<string, mixed> $context): Application $closure */
$closure = include dirname(__DIR__) . '/bin/console';

/** @var non-empty-array<non-empty-string, mixed> $context */
$context = $_SERVER;

return $closure($context);
