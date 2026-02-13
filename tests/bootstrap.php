<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

/** @var non-empty-array<non-empty-string, mixed> $_SERVER */
new Dotenv()->bootEnv(dirname(__DIR__) . '/.env');

if (true === (bool) $_SERVER['APP_DEBUG']) {
    umask(0o000);
}
