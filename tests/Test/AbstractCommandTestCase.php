<?php

declare(strict_types=1);

namespace Continuum\Tests\Test;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

abstract class AbstractCommandTestCase extends KernelTestCase
{
    /**
     * @param non-empty-string $name
     * @param array<string, mixed> $input
     * @param array<string, mixed> $options
     */
    protected static function executeCommand(string $name, array $input, array $options = []): CommandTester
    {
        $kernel = static::bootKernel();

        $application = new Application($kernel);
        $command = $application->find($name);

        $commandTester = new CommandTester($command);
        $commandTester->execute($input, $options);

        return $commandTester;
    }
}
