<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Response\Admin\Database;

use Continuum\Dto\Response\Admin\Database\BackupFile;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BackupFile::class)]
final class BackupFileTest extends TestCase
{
    public function testConstructor(): void
    {
        $dto = new BackupFile(
            name: 'f',
            size: 123,
            date: $date = new DateTimeImmutable('-1 hour'),
        );

        self::assertSame('f', $dto->name);
        self::assertSame(123, $dto->size);
        self::assertSame($date, $dto->date);
    }
}
