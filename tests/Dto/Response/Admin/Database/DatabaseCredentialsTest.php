<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Response\Admin\Database;

use Continuum\Dto\Response\Admin\Database\DatabaseCredentials;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DatabaseCredentials::class)]
final class DatabaseCredentialsTest extends TestCase
{
    public function testConstructor(): void
    {
        $dto = new DatabaseCredentials(
            user: 'user',
            password: 'pass',
            host: 'host',
            port: 1234,
            name: 'name',
        );

        self::assertSame('user', $dto->user);
        self::assertSame('pass', $dto->password);
        self::assertSame('host', $dto->host);
        self::assertSame(1234, $dto->port);
        self::assertSame('name', $dto->name);
    }
}
