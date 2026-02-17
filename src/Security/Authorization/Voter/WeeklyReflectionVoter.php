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
final class WeeklyReflectionVoter extends Voter
{
    public const string VIEW = 'WEEKLY_REFLECTION_VIEW';
    public const string PRIVATE = 'WEEKLY_REFLECTION_PRIVATE';
    public const string EDIT = 'WEEKLY_REFLECTION_EDIT';

    public function __construct(
        private readonly Security $security,
    ) {}

    #[Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::VIEW === $attribute || self::PRIVATE === $attribute || self::EDIT === $attribute;
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
            self::VIEW => $this->isViewGranted($user, $subject),
            self::PRIVATE => $this->security->isGrantedForUser($user, UserRole::Admin->value),
            self::EDIT => $this->security->isGrantedForUser($user, UserRole::SuperAdmin->value),
            default => false,
        };
    }

    private function isViewGranted(User $user, mixed $subject): bool
    {
        if (null === $subject) {
            return true;
        }

        if ($this->security->isGrantedForUser($user, UserRole::Admin->value)) {
            return true;
        }

        if (!$subject instanceof DateTimeImmutable) {
            return false;
        }

        $currentDate = new DateTimeImmutable('now', $user->timezone);

        return $currentDate->format('Y-m') === $subject->format('Y-m');
    }
}
