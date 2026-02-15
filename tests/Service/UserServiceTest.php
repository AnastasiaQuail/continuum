<?php

declare(strict_types=1);

namespace Continuum\Tests\Service;

use Continuum\Dto\Request\Admin\User\EditUser;
use Continuum\Dto\Request\User\EditLocation;
use Continuum\Entity\User;
use Continuum\Repository\UserRepositoryInterface;
use Continuum\Security\User\UserRole;
use Continuum\Security\User\UserStatus;
use Continuum\Service\UserService;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

#[CoversClass(UserService::class)]
final class UserServiceTest extends TestCase
{
    private MockObject&UserRepositoryInterface $repository;
    private UserService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserRepositoryInterface::class);
        $this->service = new UserService($this->repository);
    }

    public function testGet(): void
    {
        $user = new User('email@example.com');
        $this->repository->expects($this->once())->method('findOneById')->with($user->id)->willReturn($user);

        $foundUser = $this->service->get($user->id);

        self::assertSame('email@example.com', $foundUser->email);
    }

    public function testGetNotFound(): void
    {
        $id = Uuid::v7();
        $this->repository->expects($this->once())->method('findOneById')->with($id)->willReturn(null);

        $this->expectExceptionObject(new NotFoundHttpException('User not found'));

        $this->service->get($id);
    }

    public function testGetAll(): void
    {
        $users = [
            new User('email@example.com'),
            new User('email2@example.com'),
            new User('email3@example.com'),
        ];
        $this->repository->expects($this->once())->method('findOrdered')->willReturn($users);

        $foundUsers = $this->service->getAll();

        self::assertCount(3, $foundUsers);
    }

    public function testGetAllEmpty(): void
    {
        $this->repository->expects($this->once())->method('findOrdered')->willReturn([]);

        $foundUsers = $this->service->getAll();

        self::assertCount(0, $foundUsers);
    }

    public function testUpdateTimezone(): void
    {
        $user = new User('email@example.com');
        $user->timezone = new DateTimeZone('UTC');

        $timezone = new DateTimeZone('Asia/Shanghai');
        $this->repository->expects($this->once())->method('save')->with($user);

        $this->service->updateTimezone($user, $timezone);

        self::assertSame($timezone->getName(), $user->timezone->getName());
    }

    public function testUpdateLocation(): void
    {
        $user = new User('email@example.com');
        $dto = new EditLocation(10.10, -20.20);
        $this->repository->expects($this->once())->method('save')->with($user);

        $this->service->updateLocation($user, $dto);

        self::assertSame($dto->latitude, $user->location->getLatitude());
        self::assertSame($dto->longitude, $user->location->getLongitude());
    }

    public function testUpdate(): void
    {
        $user = new User('email@example.com');
        $dto = new EditUser(UserStatus::Disabled, [UserRole::User->value]);
        $this->repository->expects($this->once())->method('save')->with($user);
        $this->repository->expects($this->once())->method('updateRoles')->with($user->id, UserRole::User->value);

        $this->service->update($user, $dto);

        self::assertSame(UserStatus::Disabled, $user->status);
    }
}
