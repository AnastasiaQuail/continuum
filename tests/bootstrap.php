<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

/** @var Application $application */
$application = include __DIR__ . '/console-application.php';

if ($application->getKernel()->isDebug()) {
    umask(0o000);
}

if (isset($_SERVER['DATABASE_PREPARE'])) {
    $application->setCatchExceptions(boolean: false);
    $application->setAutoExit(boolean: false);

    $application->run(new ArrayInput(['command' => 'doctrine:database:drop', '--if-exists' => '1', '--force' => '1']));
    $application->run(new ArrayInput(['command' => 'doctrine:database:create']));
    $application->run(new ArrayInput(['command' => 'doctrine:schema:create']));
    $application->run(new ArrayInput(['command' => 'doctrine:fixtures:load', '--no-interaction' => '1']));

    $application->getKernel()->shutdown();
    restore_error_handler();
}
