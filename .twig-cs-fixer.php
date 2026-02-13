<?php

declare(strict_types=1);

use TwigCsFixer\Config\Config;
use TwigCsFixer\Rules\File\DirectoryNameRule;
use TwigCsFixer\Rules\File\FileExtensionRule;
use TwigCsFixer\Rules\File\FileNameRule;
use TwigCsFixer\Ruleset\Ruleset;
use TwigCsFixer\Standard\TwigCsFixer;

return new Config()
    ->setCacheFile(__DIR__ . '/var/cache/.twig-cs-fixer.json')
    ->allowNonFixableRules()
    ->setRuleset(
        new Ruleset()
            ->addStandard(new TwigCsFixer())
            ->addRule(new DirectoryNameRule(baseDirectory: 'templates', ignoredSubDirectories: ['bundles']))
            ->addRule(new FileExtensionRule())
            ->addRule(
                new FileNameRule(baseDirectory: 'templates', ignoredSubDirectories: ['bundles'], optionalPrefix: '_')
            )
    );
