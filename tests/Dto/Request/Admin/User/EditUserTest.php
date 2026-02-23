<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Request\Admin\User;

use Continuum\Dto\Request\Admin\User\EditUser;
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
            roles: [],
        );

        self::assertSame(UserStatus::Active, $dto->status);
        self::assertEmpty($dto->roles);
    }
}
