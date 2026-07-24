<?php

declare(strict_types=1);

namespace Continuum\Security\Authorization\Voter;

use Continuum\Entity\BodyMeasurement;
use Continuum\Entity\User;
use Continuum\Security\User\UserRole;
use DateTimeImmutable;
use Override;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, null|BodyMeasurement>
 */
final class MeasurementVoter extends Voter
{
    public const string VIEW = 'BODY_MEASUREMENT_VIEW';
    public const string HISTORY = 'BODY_MEASUREMENT_HISTORY';
    public const string REPORT_VIEW = 'BODY_MEASUREMENT_REPORT_VIEW';
    public const string CREATE = 'BODY_MEASUREMENT_CREATE';
    public const string EDIT = 'BODY_MEASUREMENT_EDIT';
    public const string DELETE = 'BODY_MEASUREMENT_DELETE';

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
                self::HISTORY,
                self::REPORT_VIEW,
                self::CREATE,
                self::EDIT,
                self::DELETE,
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
            self::VIEW, self::HISTORY, self::REPORT_VIEW => true,
            self::CREATE => $this->security->isGrantedForUser($user, UserRole::Admin->value),
            self::EDIT, self::DELETE => $this->isEditOrDeleteGranted($user, $subject),
            default => false,
        };
    }

    private function isEditOrDeleteGranted(User $user, mixed $subject): bool
    {
        if (!$subject instanceof BodyMeasurement) {
            return false;
        }

        $diff = new DateTimeImmutable()->diff($subject->datetime);

        if (0 === $diff->days) {
            return $this->security->isGrantedForUser($user, UserRole::Admin->value);
        }

        return $diff->days < 3
            && $this->security->isGrantedForUser($user, UserRole::SuperAdmin->value);
    }
}
