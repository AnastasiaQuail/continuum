<?php

declare(strict_types=1);

namespace Continuum\Security\Authorization\Voter;

use Continuum\Entity\User;
use Continuum\Security\User\UserRole;
use DateTimeImmutable;
use Override;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, null|DateTimeImmutable>
 */
final class MoodReflectionVoter extends Voter
{
    public const string VIEW = 'MOOD_REFLECTION_VIEW';
    public const string REPORT_VIEW = 'MOOD_REFLECTION_REPORT_VIEW';
    public const string EDIT = 'MOOD_REFLECTION_EDIT';

    public function __construct(
        private readonly Security $security,
    ) {}

    #[Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::REPORT_VIEW, self::EDIT], true);
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

        return match ($attribute) {
            self::VIEW, self::REPORT_VIEW => true,
            self::EDIT => $this->security->isGrantedForUser($user, UserRole::SuperAdmin->value),
            default => false,
        };
    }
}
