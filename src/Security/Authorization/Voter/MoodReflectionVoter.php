<?php

declare(strict_types=1);

namespace Continuum\Security\Authorization\Voter;

use Continuum\Entity\User;
use Continuum\Security\User\UserRole;
use DateMalformedStringException;
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
    public const string LAST_UNFILLED = 'MOOD_REFLECTION_LAST_UNFILLED';

    public function __construct(
        private readonly Security $security,
    ) {}

    #[Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::REPORT_VIEW, self::EDIT, self::LAST_UNFILLED], strict: true);
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
            self::EDIT => is_string($subject) && $this->isEditGranted($user, $subject),
            self::LAST_UNFILLED => $this->security->isGrantedForUser($user, UserRole::Admin->value),
            default => false,
        };
    }

    private function isEditGranted(User $user, string $subject): bool
    {
        try {
            $day = new DateTimeImmutable($subject, $user->timezone)->setTime(0, 0);
        } catch (DateMalformedStringException) {
            return false;
        }

        $now = new DateTimeImmutable('now', $user->timezone)->setTime(0, 0);

        if ($day->format('Y-m-d') > $now->format('Y-m-d')) {
            return false;
        }

        if ($now->diff($day)->days <= 3) {
            return $this->security->isGrantedForUser($user, UserRole::Admin->value);
        }

        return $now->diff($day)->days <= 14
            && $this->security->isGrantedForUser($user, UserRole::SuperAdmin->value);
    }
}
