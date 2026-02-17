<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

return new Configuration()
    ->disableExtensionsAnalysis()
    ->ignoreErrorsOnPackages(
        [
            'doctrine/doctrine-migrations-bundle',
            'symfony/apache-pack',
            'symfony/asset',
            'symfony/asset-mapper',
            'symfony/flex',
            'symfony/monolog-bundle',
            'symfony/runtime',
            'symfony/twig-bundle',
            'symfony/ux-icons',
            'symfony/yaml',
        ],
        [ErrorType::UNUSED_DEPENDENCY]
    )
    ->ignoreErrorsOnPackage(
        'symfony/dotenv',
        [ErrorType::PROD_DEPENDENCY_ONLY_IN_DEV]
    )
    ->ignoreErrorsOnPackageAndPath(
        'doctrine/doctrine-fixtures-bundle',
        __DIR__ . '/src/DataFixtures',
        [ErrorType::DEV_DEPENDENCY_IN_PROD]
    )
    ->ignoreErrorsOnPackageAndPath(
        'nikic/php-parser',
        __DIR__ . '/tools',
        [ErrorType::SHADOW_DEPENDENCY]
    );
