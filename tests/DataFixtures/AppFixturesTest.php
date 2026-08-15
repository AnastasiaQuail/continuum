<?php

declare(strict_types=1);

namespace Continuum\Tests\DataFixtures;

use Continuum\DataFixtures\AppFixtures;
use Continuum\Entity\User;
use Continuum\Security\User\UserRole;
use Continuum\Security\User\UserStatus;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Override;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(AppFixtures::class)]
final class AppFixturesTest extends TestCase
{
    private EntityManagerInterface&MockObject $manager;

    /** @var list<object> */
    private array $persisted = [];
    private MockObject&ReferenceRepository $referenceRepository;
    private MockObject&UserPasswordHasherInterface $passwordHasher;
    private AppFixtures $fixtures;

    #[Override]
    protected function setUp(): void
    {
        $this->manager = $this->createMock(EntityManagerInterface::class);
        $this->manager->method('persist')->willReturnCallback(
            function (object $entity): void {
                $this->persisted[] = $entity;
            }
        );
        $this->manager->method('getClassMetadata')
            // @phpstan-ignore argument.type (fix for phpstan, $className is not empty)
            ->willReturnCallback(static fn (string $className): ClassMetadata => new ClassMetadata($className));

        $this->referenceRepository = $this->createMock(ReferenceRepository::class);

        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->passwordHasher->method('hashPassword')->willReturn('hashed_password');

        $this->fixtures = new AppFixtures($this->passwordHasher);
        $this->fixtures->setReferenceRepository($this->referenceRepository);
    }

    public function testLoad(): void
    {
        $this->manager->expects($this->exactly(4))->method('persist');
        $this->manager->expects($this->once())->method('flush');
        $this->referenceRepository->expects($this->exactly(4))->method('addReference');
        $this->passwordHasher->expects($this->exactly(4))->method('hashPassword');

        $this->fixtures->load($this->manager);

        /** @var list<User> $users */
        $users = array_slice($this->persisted, 0, 4);
        self::assertCount(4, $users);
        self::assertSame(
            ['superadmin', 'admin', 'user', 'disabled_user'],
            array_map(static fn (User $user): string => $user->identifier, $users),
        );
        self::assertSame(
            [UserStatus::Active, UserStatus::Active, UserStatus::Active, UserStatus::Disabled],
            array_map(static fn (User $user): UserStatus => $user->status, $users),
        );
        self::assertSame(
            [
                [UserRole::User->value, UserRole::SuperAdmin->value],
                [UserRole::User->value, UserRole::Admin->value],
                [UserRole::User->value],
                [UserRole::User->value],
            ],
            array_map(static fn (User $user): array => $user->roles, $users),
        );
        self::assertSame(
            ['Asia/Istanbul', 'Europe/Berlin', 'UTC', 'UTC'],
            array_map(static fn (User $user): string => $user->timezone->getName(), $users),
        );
        self::assertSame(
            ['41.006381:28.975872', '52.517389:13.395131', '0.000000:0.000000', '0.000000:0.000000'],
            array_map(
                static fn (User $user): string => sprintf(
                    '%f:%f',
                    $user->location->latitude,
                    $user->location->longitude
                ),
                $users
            ),
        );
    }
}
