<?php

declare(strict_types=1);

use TwigCsFixer\Config\Config;
use TwigCsFixer\File\Finder;
use TwigCsFixer\Rules\File\DirectoryNameRule;
use TwigCsFixer\Rules\File\FileExtensionRule;
use TwigCsFixer\Rules\File\FileNameRule;
use TwigCsFixer\Ruleset\Ruleset;
use TwigCsFixer\Standard\TwigCsFixer;

return new Config()
    ->setCacheFile(__DIR__ . '/var/cache/.twig-cs-fixer.json')
    ->setFinder(
        Finder::create()
            ->in(__DIR__ . '/templates')
    )
    ->allowNonFixableRules()
    ->setRuleset(
        new Ruleset()
            ->addStandard(new TwigCsFixer())
            ->addRule(
                new DirectoryNameRule(baseDirectory: 'templates', ignoredSubDirectories: ['bundles', 'components'])
            )
            ->addRule(
                new DirectoryNameRule(case: DirectoryNameRule::PASCAL_CASE, baseDirectory: 'templates/components')
            )
            ->addRule(
                new FileNameRule(
                    baseDirectory: 'templates',
                    ignoredSubDirectories: ['bundles', 'components'],
                    optionalPrefix: '_',
                )
            )
            ->addRule(new FileNameRule(case: DirectoryNameRule::PASCAL_CASE, baseDirectory: 'templates/components'))
            ->addRule(new FileExtensionRule())
    );
