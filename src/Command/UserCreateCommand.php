<?php

declare(strict_types=1);

namespace Continuum\Command;

use Continuum\Entity\User;
use Continuum\Security\User\UserRole;
use Continuum\Security\User\UserStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:user:create',
    description: 'Create a new user',
)]
final readonly class UserCreateCommand
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function __invoke(
        SymfonyStyle $io,
        #[Argument]
        string $email,
        #[Argument]
        string $password,
        #[Option]
        ?UserRole $role = null,
    ): int {
        $user = new User($email);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $password)
        );

        if (null !== $role) {
            $user->addRole($role);

            if (UserRole::SuperAdmin === $role) {
                $user->setStatus(UserStatus::Active);
            }
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('The user has been created.');

        return Command::SUCCESS;
    }
}
