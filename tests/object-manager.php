<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerInterface;

/** @var ContainerInterface $container */
$container = include __DIR__ . '/symfony-container.php';

return $container->get('doctrine')->getManager();
