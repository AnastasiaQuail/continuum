<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

/** @var Application $application */
$application = include __DIR__ . '/console-application.php';
$application->setCatchExceptions(false);
$application->setAutoExit(false);

if ($application->getKernel()->isDebug()) {
    umask(0o000);
}

$application->run(new ArrayInput(['command' => 'doctrine:database:drop', '--if-exists' => '1', '--force' => '1']));
$application->run(new ArrayInput(['command' => 'doctrine:database:create']));
$application->run(new ArrayInput(['command' => 'doctrine:schema:create']));

$application->getKernel()->shutdown();
restore_error_handler();
