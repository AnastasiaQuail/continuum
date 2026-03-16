<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\CodingStyle\Rector\ClassLike\NewlineBetweenClassLikeStmtsRector;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;

return RectorConfig::configure()
    ->withCache(__DIR__ . '/var/cache/rector')
    ->withSymfonyContainerPhp(__DIR__ . '/tests/symfony-container.php')
    ->withPaths([
        __DIR__,
        __DIR__ . '/bin/console',
        __DIR__ . '/bin/phpunit',
    ])
    ->withRootFiles()
    ->withSkip([
        __DIR__ . '/assets/vendor',
        __DIR__ . '/config/reference.php',
        __DIR__ . '/var',
        __DIR__ . '/vendor',
    ])
    ->withImportNames()
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        typeDeclarationDocblocks: true,
        privatization: true,
        instanceOf: true,
        earlyReturn: true,
        rectorPreset: true,
        phpunitCodeQuality: true,
        doctrineCodeQuality: true,
        symfonyCodeQuality: true,
    )
    ->withComposerBased(
        twig: true,
        doctrine: true,
        phpunit: true,
        symfony: true,
    )
    ->withSkip([
        CatchExceptionNameMatchingTypeRector::class,
        FlipTypeControlToUseExclusiveTypeRector::class,
        NewlineBetweenClassLikeStmtsRector::class,
        PreferPHPUnitThisCallRector::class,
    ]);
