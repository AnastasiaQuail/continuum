<?php

declare(strict_types=1);

namespace Continuum\Command;

use Continuum\Entity\User;
use Continuum\Security\User\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

#[AsCommand(
    name: 'app:user-create',
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
        bool $isAdmin = false,
    ): int {
        $user = new User(Uuid::v7(), $email);
        $user->activate();
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $password)
        );

        if ($isAdmin) {
            $user->addRole(UserRole::Admin);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('The user has been created.');

        return Command::SUCCESS;
    }
}
