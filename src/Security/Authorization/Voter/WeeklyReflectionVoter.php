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
final class WeeklyReflectionVoter extends Voter
{
    public const string VIEW = 'WEEKLY_REFLECTION_VIEW';
    public const string REPORT_VIEW = 'WEEKLY_REFLECTION_REPORT_VIEW';
    public const string PRIVATE = 'WEEKLY_REFLECTION_PRIVATE';
    public const string EDIT = 'WEEKLY_REFLECTION_EDIT';

    public function __construct(
        private readonly Security $security,
    ) {}

    #[Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::REPORT_VIEW, self::PRIVATE, self::EDIT], strict: true);
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
            self::VIEW,
            self::REPORT_VIEW => null === $subject
                || ($subject instanceof DateTimeImmutable && $this->isViewGranted($user, $subject)),
            self::PRIVATE => $this->security->isGrantedForUser($user, UserRole::Admin->value),
            self::EDIT => is_string($subject) && $this->isEditGranted($user, $subject),
            default => false,
        };
    }

    private function isViewGranted(User $user, DateTimeImmutable $subject): bool
    {
        $currentDate = new DateTimeImmutable('now', $user->timezone);

        if ($currentDate->format('Y-m') === $subject->format('Y-m')) {
            return true;
        }

        // previous months
        if ($currentDate->format('Y-m') > $subject->format('Y-m')) {
            return $this->security->isGrantedForUser($user, UserRole::Admin->value);
        }

        // closest sunday is first week of the next month
        if ($currentDate->modify('sunday')->format('Y-m') === $subject->format('Y-m')) {
            return $this->security->isGrantedForUser($user, UserRole::SuperAdmin->value);
        }

        return false;
    }

    private function isEditGranted(User $user, string $subject): bool
    {
        if (!$this->security->isGrantedForUser($user, UserRole::SuperAdmin->value)) {
            return false;
        }

        try {
            $week = new DateTimeImmutable($subject, $user->timezone)->setTime(0, 0);
        } catch (DateMalformedStringException) {
            return false;
        }

        $now = new DateTimeImmutable('now', $user->timezone)->setTime(0, 0);

        return $week > $now || $now->diff($week)->days <= 14;
    }
}
