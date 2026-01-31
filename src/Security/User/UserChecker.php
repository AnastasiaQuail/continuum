<?php

declare(strict_types=1);

namespace Continuum\Security\User;

use Continuum\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        match ($user->getStatus()) {
            UserStatus::Created => throw new CustomUserMessageAccountStatusException('Account is not active yet.'),
            UserStatus::Active => null,
            UserStatus::Disabled => throw new CustomUserMessageAccountStatusException('Account is disabled.'),
        };
    }

    public function checkPostAuth(UserInterface $user, ?TokenInterface $token = null): void {}
}
