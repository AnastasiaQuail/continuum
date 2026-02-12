<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\CodingStyle\Rector\ClassLike\NewlineBetweenClassLikeStmtsRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withCache(__DIR__ . '/var/cache/rector')
    ->withSymfonyContainerPhp(__DIR__ . '/tests/symfony-container.php')
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
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
    )
    ->withSkip([
        CatchExceptionNameMatchingTypeRector::class,
        FlipTypeControlToUseExclusiveTypeRector::class,
        NewlineBetweenClassLikeStmtsRector::class,
    ]);
