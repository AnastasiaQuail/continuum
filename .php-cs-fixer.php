<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

return new Config()
    ->setCacheFile(__DIR__ . '/var/cache/.php-cs-fixer.json')
    ->setFinder(
        Finder::create()
            ->in(__DIR__)
            ->exclude(['var'])
            ->append([
                'bin/console',
                'bin/phpunit',
                __FILE__,
            ])
    )
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHP8x5Migration' => true,
        '@PHP8x5Migration:risky' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        'attribute_empty_parentheses' => true,
        'date_time_immutable' => true,
        'concat_space' => ['spacing' => 'one'],
        'final_class' => true,
        'final_public_method_for_abstract_class' => true,
        'global_namespace_import' => false,
        'native_function_invocation' => false,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'phpdoc_align' => ['align' => 'left'],

        'ordered_class_elements' => false, // todo enable
        'octal_notation' => false, // todo enable

        // '@PHPUnit100Migration:risky' => true,
        // 'get_class_to_class_keyword' => true,
        // 'modernize_strpos' => true,
        // 'phpdoc_align' => ['align' => 'left'],
        // 'phpdoc_array_type' => true,
        // 'phpdoc_list_type' => true,
        // 'phpdoc_param_order' => true,
    ]);
