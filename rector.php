<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withCache(__DIR__ . '/var/cache/rector')
    ->withSymfonyContainerPhp(__DIR__ . '/tests/symfony-container.php')
    ->withParallel()
    ->withRootFiles()
    ->withPaths([
        __DIR__ . '/bin',
        __DIR__ . '/config',
        __DIR__ . '/migrations',
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/tools',
    ])
    ->withSkip([
        __DIR__ . '/tools/vendor/*',
    ])
    ->withImportNames()
    ->withPhpSets();
