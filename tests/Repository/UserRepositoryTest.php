<?php

declare(strict_types=1);

namespace Continuum\Tests\Repository;

use Continuum\Entity\User;
use Continuum\Repository\UserRepository;
use Continuum\Security\User\UserRole;
use Continuum\Tests\Test\AbstractRepositoryTestCase;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionProperty;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[CoversClass(UserRepository::class)]
final class UserRepositoryTest extends AbstractRepositoryTestCase
{
    private UserRepository $repository;

    #[Override]
    protected function setUp(): void
    {
        $this->repository = self::getContainer()->get(UserRepository::class);
    }

    public function testFindById(): void
    {
        $user = $this->repository->findOneBy(['identifier' => 'superadmin']);
        self::assertNotNull($user);
        self::clearManager();

        $foundUser = $this->repository->findOneById($user->id);

        self::assertNotNull($foundUser);
        self::assertSame($user->identifier, $foundUser->identifier);
    }

    public function testFindOrdered(): void
    {
        $newUsers = [
            'username_five' => '+1 month',
            'username_six' => '+1 day',
        ];

        foreach ($newUsers as $identifier => $date) {
            $newUser = new User($identifier);
            $newUser->password = 'password';

            new ReflectionProperty(User::class, 'lastVisitedAt')
                ->setValue($newUser, $newUser->lastVisitedAt->modify($date));

            $this->repository->save($newUser);
        }

        $users = $this->repository->findOrdered();

        self::assertCount(6, $users);
        self::assertSame('username_five', $users[0]->identifier);
        self::assertSame('username_six', $users[1]->identifier);
    }

    public function testUpgradePasswordWrongUser(): void
    {
        $user = new class implements PasswordAuthenticatedUserInterface {
            #[Override]
            public function getPassword(): ?string
            {
                return null;
            }
        };

        $this->expectExceptionObject(
            new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class))
        );

        $this->repository->upgradePassword($user, 'newPassword');
    }

    public function testUpgradePassword(): void
    {
        $user = $this->repository->findOneBy(['identifier' => 'superadmin']);
        self::assertNotNull($user);

        $this->repository->upgradePassword($user, 'newPassword');

        self::clearManager();
        self::assertNotNull($upgradedUser = $this->repository->findOneById($user->id));
        self::assertSame('newPassword', $upgradedUser->password);
    }

    public function testUpgradeEmptyPassword(): void
    {
        $user = new User('username');

        $this->expectExceptionObject(new UnsupportedUserException('New password cannot be blank.'));

        $this->repository->upgradePassword($user, '');
    }

    public function testUpgradeRoles(): void
    {
        $user = $this->repository->findOneBy(['identifier' => 'superadmin']);
        self::assertNotNull($user);

        $this->repository->updateRoles($user->id, UserRole::User->value);

        self::clearManager();
        self::assertNotNull($updatedUser = $this->repository->findOneById($user->id));
        self::assertSame([UserRole::User->value], $updatedUser->getRoles());
        self::assertSame([UserRole::User->value, UserRole::SuperAdmin->value], $user->getRoles());
    }

    public function testUpdateLastVisitedAt(): void
    {
        $user = $this->repository->findOneBy(['identifier' => 'superadmin']);
        self::assertNotNull($user);
        $newLastVisited = $user->lastVisitedAt->modify('+1 month');

        $this->repository->updateLastVisitedAt($user->id, $newLastVisited);

        self::clearManager();
        self::assertNotNull($updatedUser = $this->repository->findOneById($user->id));
        self::assertSame($newLastVisited->getTimestamp(), $updatedUser->lastVisitedAt->getTimestamp());
        self::assertNotSame($user->lastVisitedAt->getTimestamp(), $updatedUser->lastVisitedAt->getTimestamp());
    }
}
