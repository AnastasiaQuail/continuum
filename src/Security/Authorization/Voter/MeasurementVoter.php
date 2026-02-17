<?php

declare(strict_types=1);

namespace Continuum\Security\Authorization\Voter;

use Continuum\Entity\BodyMeasurement;
use Continuum\Entity\User;
use Continuum\Security\User\UserRole;
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
    public const string CREATE = 'BODY_MEASUREMENT_CREATE';
    public const string EDIT = 'BODY_MEASUREMENT_EDIT';

    public function __construct(
        private readonly Security $security,
    ) {}

    #[Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::VIEW === $attribute || self::CREATE === $attribute || self::EDIT === $attribute;
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
            self::VIEW => true,
            self::CREATE => $this->security->isGrantedForUser($user, UserRole::Admin->value),
            self::EDIT => $subject instanceof BodyMeasurement
                && $this->security->isGrantedForUser($user, UserRole::SuperAdmin->value),
            default => false,
        };
    }
}
