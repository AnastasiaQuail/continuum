<?php

declare(strict_types=1);

namespace Continuum\Tests\Entity;

use Continuum\Entity\Location;
use Continuum\Entity\User;
use Continuum\Security\User\UserRole;
use Continuum\Security\User\UserStatus;
use DateTimeZone;
use InvalidArgumentException;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\UuidV7;

#[CoversClass(User::class)]
final class UserTest extends TestCase
{
    public function testCreate(): void
    {
        $user = new User('email@example.com');
        $user->password = 'hashed_password';

        self::assertInstanceOf(UuidV7::class, $user->id);
        self::assertSame('email@example.com', $user->email);
        self::assertSame($user->email, $user->getUserIdentifier());
        self::assertSame('hashed_password', $user->password);
        self::assertSame($user->password, $user->getPassword());
        self::assertSame(UserStatus::Created, $user->status);
        self::assertSame([UserRole::User->value], $user->roles);
        self::assertSame($user->roles, $user->getRoles());
        self::assertSame($user->createdAt->getTimestamp(), $user->updatedAt->getTimestamp());
        self::assertSame($user->createdAt->getTimestamp(), $user->lastVisitedAt->getTimestamp());
        self::assertSame(date_default_timezone_get(), $user->timezone->getName());
        self::assertSame(0.0, $user->location->getLatitude());
        self::assertSame(0.0, $user->location->getLongitude());
    }

    public function testEmptyEmail(): void
    {
        $this->expectExceptionObject(new InvalidArgumentException('Email cannot be empty.'));

        new User('');
    }

    public function testUninitializedPassword(): void
    {
        $this->expectExceptionObject(new InvalidArgumentException('Password should be set.'));

        $user = new User('email@example.com');

        self::assertSame('--- this assert only for phpstorm ---', $user->password);
    }

    public function testEmptyPassword(): void
    {
        $this->expectExceptionObject(new InvalidArgumentException('Password cannot be empty.'));

        $user = new User('email@example.com');
        $user->password = '';

        self::assertSame('--- this assert only for phpstorm ---', $user->password);
    }

    public function testStatus(): void
    {
        $user = new User('email@example.com');

        foreach (UserStatus::cases() as $userStatus) {
            $user->status = $userStatus;

            self::assertSame($userStatus, $user->status);
        }
    }

    /**
     * @param list<UserRole> $addRoles
     * @param list<UserRole> $resultRoles
     */
    #[DataProvider('provideRolesCases')]
    public function testRoles(array $addRoles, array $resultRoles): void
    {
        $user = new User('email@example.com');

        foreach ($addRoles as $role) {
            $user->addRole($role);
        }

        self::assertSame(
            array_map(
                static fn (UserRole $role): string => $role->value,
                $resultRoles
            ),
            $user->roles
        );
    }

    /**
     * @return iterable<array{0: list<UserRole>, 1: list<UserRole>}>
     */
    public static function provideRolesCases(): iterable
    {
        yield [
            [],
            [UserRole::User],
        ];

        yield [
            [UserRole::User],
            [UserRole::User],
        ];

        yield [
            [UserRole::Admin],
            [UserRole::User, UserRole::Admin],
        ];

        yield [
            [UserRole::SuperAdmin],
            [UserRole::User, UserRole::SuperAdmin],
        ];

        yield [
            [UserRole::Admin, UserRole::SuperAdmin],
            [UserRole::User, UserRole::Admin, UserRole::SuperAdmin],
        ];
    }

    /**
     * @param non-empty-string $timezoneName
     */
    #[DataProvider('provideTimezoneCases')]
    public function testTimezone(string $timezoneName): void
    {
        $user = new User('email@example.com');

        $user->timezone = new DateTimeZone($timezoneName);

        self::assertSame($timezoneName, $user->timezone->getName());
    }

    /**
     * @return iterable<array{0: non-empty-string}>
     */
    public static function provideTimezoneCases(): iterable
    {
        yield ['UTC'];

        yield ['Asia/Tokyo'];

        yield ['America/Bogota'];

        yield ['Australia/Melbourne'];
    }

    public function testLocation(): void
    {
        $user = new User('email@example.com');

        $user->location = new Location('100.123456', '9.876543');

        self::assertSame(100.123456, $user->location->getLatitude());
        self::assertSame(9.876543, $user->location->getLongitude());
    }

    #[DataProvider('provideNotEqualedCases')]
    public function testNotEqualed(UserInterface $otherUser): void
    {
        $user = new User('email@example.com');
        $user->password = 'hashed_password';
        $user->status = UserStatus::Active;
        $user->addRole(UserRole::Admin);

        self::assertFalse($user->isEqualTo($otherUser));
    }

    /**
     * @return iterable<array{0: UserInterface}>
     */
    public static function provideNotEqualedCases(): iterable
    {
        $userOtherImplement = new class implements UserInterface {
            /**
             * @return list<non-empty-string>
             */
            #[Override]
            public function getRoles(): array
            {
                return [];
            }

            #[Override]
            public function getUserIdentifier(): string
            {
                return '-';
            }
        };

        yield [$userOtherImplement];

        $userOtherEmail = new User('other-email@example.com');

        yield [$userOtherEmail];

        $userDefaultStatus = new User('email@example.com');

        yield [$userDefaultStatus];

        $userOtherStatus = new User('email@example.com');
        $userOtherStatus->status = UserStatus::Disabled;

        yield [$userOtherStatus];

        $userOtherPassword = new User('email@example.com');
        $userOtherPassword->password = 'other_hashed_password';

        yield [$userOtherPassword];

        $userDefaultRole = new User('email@example.com');
        $userDefaultRole->password = 'hashed_password';

        yield [$userDefaultRole];

        $userOtherRole = new User('email@example.com');
        $userOtherRole->password = 'hashed_password';
        $userOtherRole->addRole(UserRole::SuperAdmin);

        yield [$userOtherRole];

        $userOtherRoles = new User('email@example.com');
        $userOtherRoles->password = 'hashed_password';
        $userOtherRoles->addRole(UserRole::Admin);
        $userOtherRoles->addRole(UserRole::SuperAdmin);

        yield [$userOtherRoles];
    }

    public function testEqual(): void
    {
        $user = new User('email@example.com');
        $user->password = 'hashed_password';
        $user->status = UserStatus::Active;
        $user->addRole(UserRole::Admin);

        $otherUser = new User('email@example.com');
        $otherUser->password = 'hashed_password';
        $otherUser->status = UserStatus::Active;
        $otherUser->addRole(UserRole::Admin);
        $otherUser->timezone = new DateTimeZone('Africa/Tunis');
        $otherUser->location = new Location('100.12', '-50.80');

        self::assertTrue($user->isEqualTo($otherUser));
    }
}
