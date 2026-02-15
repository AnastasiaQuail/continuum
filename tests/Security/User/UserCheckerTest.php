<?php

declare(strict_types=1);

namespace Continuum\Tests\Security\User;

use Continuum\Entity\User;
use Continuum\Security\User\UserChecker;
use Continuum\Security\User\UserStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserInterface;
use Throwable;

#[CoversClass(UserChecker::class)]
final class UserCheckerTest extends TestCase
{
    public function testCheckPreAuthWrongImplement(): void
    {
        $userChecker = new UserChecker();
        $user = new class implements UserInterface {
            /**
             * @return list<non-empty-string>
             */
            public function getRoles(): array
            {
                return [];
            }

            public function getUserIdentifier(): string
            {
                return '-';
            }
        };

        $this->expectNotToPerformAssertions();

        $userChecker->checkPreAuth($user);
    }

    #[DataProvider('provideCheckPreAuthCases')]
    public function testCheckPreAuth(UserStatus $status, ?Throwable $exception): void
    {
        $userChecker = new UserChecker();
        $user = new User('email@example.com');
        $user->status = $status;

        if (null === $exception) {
            $this->expectNotToPerformAssertions();
        } else {
            $this->expectExceptionObject($exception);
        }

        $userChecker->checkPreAuth($user);
    }

    /**
     * @return iterable<array{0: UserStatus, 1: null|Throwable}>
     */
    public static function provideCheckPreAuthCases(): iterable
    {
        yield [
            UserStatus::Created,
            new CustomUserMessageAccountStatusException('Account is not active yet.'),
        ];

        yield [
            UserStatus::Active,
            null,
        ];

        yield [
            UserStatus::Disabled,
            new CustomUserMessageAccountStatusException('Account is disabled.'),
        ];
    }
}
