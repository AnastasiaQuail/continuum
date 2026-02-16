<?php

declare(strict_types=1);

namespace Continuum\Tests\Entity;

use Continuum\Entity\Location;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Location::class)]
final class LocationTest extends TestCase
{
    public function testCreate(): void
    {
        $location = new Location(
            latitude: '123.456789',
            longitude: '-98.765432',
        );

        self::assertSame(123.456789, $location->getLatitude());
        self::assertSame(-98.765432, $location->getLongitude());
    }
}
