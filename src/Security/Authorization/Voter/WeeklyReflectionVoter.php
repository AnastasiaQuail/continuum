<?php

declare(strict_types=1);

namespace Continuum\Security\Authorization\Voter;

use Continuum\Entity\User;
use Continuum\Security\User\UserRole;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class WeeklyReflectionVoter extends Voter
{
    public const string VIEW = 'WEEKLY_REFLECTION_VIEW';
    public const string EDIT = 'WEEKLY_REFLECTION_EDIT';

    public function __construct(
        private readonly Security $security,
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::VIEW === $attribute || self::EDIT === $attribute;
    }

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token,
        ?Vote $vote = null
    ): bool {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return match ($attribute) {
            self::VIEW => $this->security->isGrantedForUser($user, UserRole::Admin->value),
            self::EDIT => $this->security->isGrantedForUser($user, UserRole::SuperAdmin->value),
        };
    }
}
