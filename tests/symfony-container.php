<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Console\Application;

/** @var Application $application */
$application = include __DIR__ . '/console-application.php';

$kernel = $application->getKernel();
$kernel->boot();

return $kernel->getContainer();
