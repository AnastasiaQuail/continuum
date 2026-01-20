<?php

declare(strict_types=1);

namespace Continuum\Security\Authorization\Voter;

use Continuum\Entity\User;
use Continuum\Security\User\UserRole;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class CalendarVoter extends Voter
{
    public const string VIEW = 'CALENDAR_VIEW';
    public const string UPCOMING = 'CALENDAR_UPCOMING_NOTIFICATIONS';
    public const string EDIT = 'CALENDAR_EDIT';
    public const string EVENT_DELETE = 'CALENDAR_EVENT_DELETE';

    public function __construct(
        private readonly Security $security,
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::VIEW === $attribute
            || self::UPCOMING === $attribute
            || self::EDIT === $attribute
            || self::EVENT_DELETE === $attribute;
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
            self::VIEW => true,
            self::UPCOMING, self::EDIT => $this->security->isGrantedForUser($user, UserRole::Admin->value),
            self::EVENT_DELETE => $this->security->isGrantedForUser($user, UserRole::SuperAdmin->value),
        };
    }
}
