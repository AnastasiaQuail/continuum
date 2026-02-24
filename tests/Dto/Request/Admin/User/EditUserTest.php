<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Request\Admin\User;

use Continuum\Dto\Request\Admin\User\EditUser;
use Continuum\Security\User\UserRole;
use Continuum\Security\User\UserStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EditUser::class)]
final class EditUserTest extends TestCase
{
    public function testConstructor(): void
    {
        $dto = new EditUser(
            status: UserStatus::Active,
            roles: [UserRole::User->value],
        );

        self::assertSame(UserStatus::Active, $dto->status);
        self::assertSame([UserRole::User->value], $dto->roles);
    }

    public function testValues(): void
    {
        $expected = [];
        foreach (UserRole::cases() as $role) {
            $expected[] = $role->value;
        }

        $roles = EditUser::values();

        self::assertSame($expected, $roles);
    }
}
