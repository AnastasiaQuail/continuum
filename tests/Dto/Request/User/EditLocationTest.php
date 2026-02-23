<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Request\User;

use Continuum\Dto\Request\User\EditLocation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EditLocation::class)]
final class EditLocationTest extends TestCase
{
    public function testConstructor(): void
    {
        $dto = new EditLocation(
            latitude: 10.0,
            longitude: 20.0,
        );

        self::assertSame(10.0, $dto->latitude);
        self::assertSame(20.0, $dto->longitude);
    }
}
