<?php

declare(strict_types=1);

namespace Continuum\Tests\Service;

use Continuum\Entity\User;
use Continuum\Service\GodUserService;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(GodUserService::class)]
final class GodUserServiceTest extends TestCase
{
    /**
     * @param non-negative-int $age
     */
    #[DataProvider('provideGetAgeCases')]
    public function testGetAge(DateTimeZone $timezone, DateTimeImmutable $date, int $age): void
    {
        $user = new User('username');
        $user->timezone = $timezone;

        $service = new GodUserService(userBirthDate: '2000-01-01', userHeight: 0);

        $userAge = $service->getAge($user, $date);

        self::assertSame($age, $userAge);
    }

    /**
     * @return iterable<array{0: DateTimeZone, 1: DateTimeImmutable, 2: non-negative-int}>
     */
    public static function provideGetAgeCases(): iterable
    {
        yield [
            new DateTimeZone('UTC'),
            new DateTimeImmutable('2000-01-01 00:00:00', new DateTimeZone('UTC')),
            0,
        ];

        yield [
            new DateTimeZone('UTC'),
            new DateTimeImmutable('2000-12-31 23:59:59', new DateTimeZone('UTC')),
            0,
        ];

        yield [
            new DateTimeZone('UTC'),
            new DateTimeImmutable('2001-01-01 00:00:00', new DateTimeZone('UTC')),
            1,
        ];

        yield [
            new DateTimeZone('UTC'),
            new DateTimeImmutable('2001-01-01 00:59:59', new DateTimeZone('Europe/Amsterdam')), // +1 hour
            0,
        ];

        yield [
            new DateTimeZone('UTC'),
            new DateTimeImmutable('2001-01-01 01:00:00', new DateTimeZone('Europe/Amsterdam')), // +1 hour
            1,
        ];

        yield [
            new DateTimeZone('America/Bogota'), // -5 hours
            new DateTimeImmutable('2001-01-01 04:59:59', new DateTimeZone('UTC')),
            0,
        ];

        yield [
            new DateTimeZone('America/Bogota'), // -5 hours
            new DateTimeImmutable('2001-01-01 05:00:00', new DateTimeZone('UTC')),
            1,
        ];

        yield [
            new DateTimeZone('Asia/Baku'), // +4 hours
            new DateTimeImmutable('2000-12-31 19:59:59', new DateTimeZone('UTC')),
            0,
        ];

        yield [
            new DateTimeZone('Asia/Baku'), // +4 hours
            new DateTimeImmutable('2000-12-31 20:00:00', new DateTimeZone('UTC')),
            1,
        ];
    }

    public function testGetHeight(): void
    {
        $service = new GodUserService(userBirthDate: '', userHeight: 150);

        $height = $service->getHeight();

        self::assertSame(150, $height);
    }
}
