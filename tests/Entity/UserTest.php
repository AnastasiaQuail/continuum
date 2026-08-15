<?php

declare(strict_types=1);

namespace Continuum\Tests\Entity;

use Continuum\Entity\Location;
use Continuum\Entity\User;
use Continuum\Security\User\UserRole;
use Continuum\Security\User\UserStatus;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\UuidV7;

#[CoversClass(User::class)]
final class UserTest extends TestCase
{
    public function testCreate(): void
    {
        $user = new User('username');
        $user->password = 'hashed_password';

        self::assertInstanceOf(UuidV7::class, $user->id);
        self::assertSame('username', $user->identifier);
        self::assertSame($user->identifier, $user->getUserIdentifier());
        self::assertSame('hashed_password', $user->password);
        self::assertSame($user->password, $user->getPassword());
        self::assertSame(UserStatus::Created, $user->status);
        self::assertSame([UserRole::User->value], $user->roles);
        self::assertSame($user->roles, $user->getRoles());
        self::assertSame($user->createdAt->getTimestamp(), $user->updatedAt->getTimestamp());
        self::assertSame($user->createdAt->getTimestamp(), $user->lastVisitedAt->getTimestamp());
        self::assertSame(date_default_timezone_get(), $user->timezone->getName());
        self::assertSame(0.0, $user->location->latitude);
        self::assertSame(0.0, $user->location->longitude);
    }

    public function testSerialize(): void
    {
        $location = new Location(100.12, -50.80);

        $user = new User('username');
        $user->password = 'hashed_password';
        $user->status = UserStatus::Active;
        $user->addRole(UserRole::Admin);
        $user->timezone = new DateTimeZone('Africa/Tunis');
        $user->location = $location;

        $data = $user->__serialize();

        self::assertSame(
            [
                'id' => $user->id,
                'password' => 'hashed_password',
                'status' => UserStatus::Active,
                'roles' => [UserRole::User->value, UserRole::Admin->value],
                'createdAt' => $user->createdAt,
                'updatedAt' => $user->createdAt,
                'lastVisitedAt' => $user->createdAt,
                'location' => $location,
                "\0" . User::class . "\0timezoneName" => 'Africa/Tunis',
                'identifier' => 'username',
                "\0" . User::class . "\0password" => hash('crc32c', 'hashed_password'),
            ],
            $data
        );
    }

    public function testEmptyIdentifier(): void
    {
        $this->expectExceptionObject(new InvalidArgumentException('Username cannot be empty.'));

        new User('');
    }

    public function testUninitializedPassword(): void
    {
        $this->expectExceptionObject(new InvalidArgumentException('Password should be set.'));

        $user = new User('username');

        self::assertSame('--- this assert only for phpstorm ---', $user->password);
    }

    public function testEmptyPassword(): void
    {
        $this->expectExceptionObject(new InvalidArgumentException('Password cannot be empty.'));

        $user = new User('username');
        $user->password = '';

        self::assertSame('--- this assert only for phpstorm ---', $user->password);
    }

    public function testStatus(): void
    {
        $user = new User('username');

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
        $user = new User('username');

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
        $user = new User('username');

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
        $user = new User('username');

        $user->location = new Location(100.123456, 9.876543);

        self::assertSame(100.123456, $user->location->latitude);
        self::assertSame(9.876543, $user->location->longitude);
    }

    #[DataProvider('provideNotEqualedCases')]
    public function testNotEqualed(UserInterface $otherUser): void
    {
        $user = new User('username');
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

        $userOtherUsername = new User('other-username');

        yield [$userOtherUsername];

        $userDefaultStatus = new User('username');

        yield [$userDefaultStatus];

        $userOtherStatus = new User('username');
        $userOtherStatus->status = UserStatus::Disabled;

        yield [$userOtherStatus];

        $userOtherPassword = new User('username');
        $userOtherPassword->status = UserStatus::Active;
        $userOtherPassword->password = 'other_hashed_password';
        $userOtherPassword->addRole(UserRole::Admin);

        yield [$userOtherPassword];

        $userDefaultRole = new User('username');
        $userDefaultRole->status = UserStatus::Active;
        $userDefaultRole->password = 'hashed_password';

        yield [$userDefaultRole];

        $userOtherRole = new User('username');
        $userOtherRole->status = UserStatus::Active;
        $userOtherRole->password = 'hashed_password';
        $userOtherRole->addRole(UserRole::SuperAdmin);

        yield [$userOtherRole];

        $userOtherRoles = new User('username');
        $userOtherRoles->status = UserStatus::Active;
        $userOtherRoles->password = 'hashed_password';
        $userOtherRoles->addRole(UserRole::Admin);
        $userOtherRoles->addRole(UserRole::SuperAdmin);

        yield [$userOtherRoles];
    }

    public function testSerializedPasswordNotEqualed(): void
    {
        $otherUser = new User('username');
        $otherUser->password = 'hashed_password';
        $otherUser->status = UserStatus::Active;
        $otherUser->addRole(UserRole::Admin);

        $user = new User('username');
        $user->password = '12345678';
        $user->status = UserStatus::Active;
        $user->addRole(UserRole::Admin);

        self::assertFalse($user->isEqualTo($otherUser));
    }

    public function testSerializedPasswordEqual(): void
    {
        $otherUser = new User('username');
        $otherUser->password = 'hashed_password';
        $otherUser->status = UserStatus::Active;
        $otherUser->addRole(UserRole::Admin);

        $user = new User('username');
        $user->password = hash('crc32c', $otherUser->password);
        $user->status = UserStatus::Active;
        $user->addRole(UserRole::Admin);

        self::assertTrue($user->isEqualTo($otherUser));
    }

    public function testEqual(): void
    {
        $user = new User('username');
        $user->password = 'hashed_password';
        $user->status = UserStatus::Active;
        $user->addRole(UserRole::Admin);

        $otherUser = new User('username');
        $otherUser->password = 'hashed_password';
        $otherUser->status = UserStatus::Active;
        $otherUser->addRole(UserRole::Admin);
        $otherUser->timezone = new DateTimeZone('Africa/Tunis');
        $otherUser->location = new Location(100.12, -50.80);

        self::assertTrue($user->isEqualTo($otherUser));
    }

    public function testUpdate(): void
    {
        $user = new User('username');
        new ReflectionProperty(User::class, 'updatedAt')
            ->setValue($user, $user->updatedAt->modify('-1 year'));

        $user->update();

        self::assertSame(new DateTimeImmutable()->format('Y-m-d H:i'), $user->updatedAt->format('Y-m-d H:i'));
    }
}
