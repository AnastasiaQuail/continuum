<?php

declare(strict_types=1);

namespace Continuum\DataFixtures;

use Continuum\Entity\Location;
use Continuum\Entity\User;
use Continuum\Security\User\UserRole;
use Continuum\Security\User\UserStatus;
use DateTimeZone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Override;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    #[Override]
    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
    }

    private function loadUsers(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as [$email, $password, $status, $roles, $timezone, $location]) {
            $user = new User($email);
            $user->password = $this->passwordHasher->hashPassword($user, $password);
            $user->status = $status;

            foreach ($roles as $role) {
                $user->addRole($role);
            }

            $user->timezone = $timezone;
            if (null !== $location) {
                $user->location = $location;
            }

            $manager->persist($user);

            $this->addReference($email, $user);
        }

        $manager->flush();
    }

    /**
     * @codeCoverageIgnore
     *
     * @return list<array{non-empty-string, non-empty-string, UserStatus, list<UserRole>, DateTimeZone, null|Location}>
     */
    private function getUserData(): array
    {
        return [
            [
                'superadmin@continuum.com',
                'password',
                UserStatus::Active,
                [UserRole::SuperAdmin],
                new DateTimeZone('Asia/Istanbul'),
                new Location(41.006381, 28.975872),
            ],
            [
                'admin@continuum.com',
                'password',
                UserStatus::Active,
                [UserRole::Admin],
                new DateTimeZone('Europe/Berlin'),
                new Location(52.517389, 13.395131),
            ],
            [
                'user@continuum.com',
                'password',
                UserStatus::Active,
                [],
                new DateTimeZone('UTC'),
                null,
            ],
            [
                'disabled_user@continuum.com',
                'password',
                UserStatus::Disabled,
                [],
                new DateTimeZone('UTC'),
                null,
            ],
        ];
    }
}
