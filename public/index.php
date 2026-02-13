<?php

declare(strict_types=1);

use Continuum\Kernel;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return static function (array $context): Kernel {
    /** @var array{APP_ENV: non-empty-string, APP_DEBUG: numeric-string} $context */
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
