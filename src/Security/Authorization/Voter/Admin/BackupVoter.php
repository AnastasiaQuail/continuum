<?php

declare(strict_types=1);

namespace Continuum\Security\Authorization\Voter\Admin;

use Continuum\Entity\User;
use Continuum\Security\User\UserRole;
use Override;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, null>
 */
final class BackupVoter extends Voter
{
    public const string VIEW = 'ADMIN_BACKUP_VIEW';
    public const string CREATE = 'ADMIN_BACKUP_CREATE';

    public function __construct(
        private readonly Security $security,
    ) {}

    #[Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::CREATE], true);
    }

    #[Override]
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

        return $this->security->isGrantedForUser($user, UserRole::Admin->value);
    }
}
