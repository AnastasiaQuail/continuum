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
final class ReportVoter extends Voter
{
    public const string MONTH_VIEW = 'REPORT_MONTH_VIEW';

    public function __construct(
        private readonly Security $security,
    ) {}

    #[Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::MONTH_VIEW === $attribute;
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

        $currentDate = new DateTimeImmutable('now', $user->timezone);
        $prevMonth = $currentDate->modify('last day of previous month')->format('Y-m');

        $dateMonth = $subject instanceof DateTimeImmutable ? $subject->format('Y-m') : null;

        // Prevent viewing of months that have not yet ended
        if ($dateMonth > $prevMonth) {
            // Access for current month
            if ($dateMonth === $currentDate->format('Y-m')) {
                return $this->security->isGrantedForUser($user, UserRole::SuperAdmin->value);
            }

            return false;
        }

        // Access only during the first three days and only the previous month
        if (
            (null === $dateMonth || $prevMonth === $dateMonth)
            && (3 < (int) $currentDate->format('j'))
        ) {
            return true;
        }

        return $this->security->isGrantedForUser($user, UserRole::Admin->value);
    }
}
