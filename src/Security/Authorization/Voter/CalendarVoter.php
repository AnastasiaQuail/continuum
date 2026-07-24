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
 * @extends Voter<string, null|int>
 */
final class CalendarVoter extends Voter
{
    public const string VIEW = 'CALENDAR_VIEW';
    public const string REPORT_VIEW = 'CALENDAR_REPORT_VIEW';
    public const string UPCOMING = 'CALENDAR_UPCOMING_EVENTS';
    public const string EDIT = 'CALENDAR_EDIT';
    public const string EVENT_DELETE = 'CALENDAR_EVENT_DELETE';

    public function __construct(
        private readonly Security $security,
    ) {}

    #[Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array(
            $attribute,
            [
                self::VIEW,
                self::REPORT_VIEW,
                self::UPCOMING,
                self::EDIT,
                self::EVENT_DELETE,
            ],
            strict: true
        );
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
            self::VIEW, self::REPORT_VIEW, self::UPCOMING => true,
            self::EDIT => is_string($subject) && $this->isEditGranted($user, $subject),
            self::EVENT_DELETE => $this->security->isGrantedForUser($user, UserRole::SuperAdmin->value),
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

        if ($day > $now || $now->diff($day)->days <= 3) {
            return $this->security->isGrantedForUser($user, UserRole::Admin->value);
        }

        return $now->diff($day)->days <= 14
            && $this->security->isGrantedForUser($user, UserRole::SuperAdmin->value);
    }
}
