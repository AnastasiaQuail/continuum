<?php

declare(strict_types=1);

namespace Continuum\Security\Authorization\Voter;

use Continuum\Entity\User;
use DateTimeImmutable;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class FutureMonthVoter extends Voter
{
    public const string ATTRIBUTE = 'FUTURE_MONTH';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::ATTRIBUTE === $attribute;
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

        if ($subject === null) {
            return true;
        }

        if (!$subject instanceof DateTimeImmutable) {
            return false;
        }

        $currentDate = new DateTimeImmutable('now', $user->getTimezone());

        return $currentDate->format('Y-m') >= $subject->format('Y-m');
    }
}
