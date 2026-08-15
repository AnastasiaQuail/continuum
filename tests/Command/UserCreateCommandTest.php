<?php

declare(strict_types=1);

namespace Continuum\Tests\Command;

use Continuum\Command\UserCreateCommand;
use Continuum\Entity\User;
use Continuum\Repository\UserRepository;
use Continuum\Repository\UserRepositoryInterface;
use Continuum\Security\User\UserRole;
use Continuum\Security\User\UserStatus;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[CoversClass(UserCreateCommand::class)]
final class UserCreateCommandTest extends KernelTestCase
{
    private UserRepository $repository;
    private UserPasswordHasherInterface $passwordHasher;

    #[Override]
    protected function setUp(): void
    {
        $this->repository = self::getContainer()->get(UserRepositoryInterface::class);
        $this->passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
    }

    /**
     * @param list<UserRole> $resultRoles
     */
    #[DataProvider('provideInvokeCases')]
    public function testInvoke(?UserRole $role, array $resultRoles, UserStatus $resultStatus): void
    {
        $input = [
            'identifier' => 'username',
            'password' => 'password',
        ];
        if (null !== $role) {
            $input['--role'] = $role->value;
        }

        $commandTester = self::runCommand('app:user:create', $input);

        self::assertSame(Command::SUCCESS, $commandTester->statusCode);
        self::assertStringContainsString('The user has been created.', $commandTester->getDisplay());
        self::assertInstanceOf(User::class, $user = $this->repository->findOneBy(['identifier' => 'username']));
        self::assertTrue($this->passwordHasher->isPasswordValid($user, 'password'));
        self::assertSame(
            array_map(
                static fn (UserRole $role): string => $role->value,
                $resultRoles
            ),
            $user->roles
        );
        self::assertSame($resultStatus, $user->status);
    }

    /**
     * @return iterable<array{0: null|UserRole, 1: list<UserRole>, 2: UserStatus}>
     */
    public static function provideInvokeCases(): iterable
    {
        yield [
            null,
            [UserRole::User],
            UserStatus::Created,
        ];

        yield [
            UserRole::User,
            [UserRole::User],
            UserStatus::Created,
        ];

        yield [
            UserRole::Admin,
            [UserRole::User, UserRole::Admin],
            UserStatus::Created,
        ];

        yield [
            UserRole::SuperAdmin,
            [UserRole::User, UserRole::SuperAdmin],
            UserStatus::Active,
        ];
    }
}
