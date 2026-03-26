<?php

declare(strict_types=1);

namespace Continuum\Tests\Enum;

use Continuum\Enum\Change;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Change::class)]
final class ChangeTest extends TestCase
{
    public function testIsUnchanged(): void
    {
        foreach (Change::cases() as $change) {
            self::assertSame('unchanged' === $change->value, $change->isUnchanged());
        }
    }
}
