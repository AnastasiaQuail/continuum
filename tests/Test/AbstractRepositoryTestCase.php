<?php

declare(strict_types=1);

namespace Continuum\Tests\Test;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractRepositoryTestCase extends KernelTestCase
{
    protected static function clearManager(): void
    {
        self::getContainer()->get('doctrine')->getManager()->clear();
    }
}
