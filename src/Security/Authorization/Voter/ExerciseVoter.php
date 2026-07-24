<?php

declare(strict_types=1);

namespace Continuum\Security\Authorization\Voter;

use Continuum\Entity\Exercise;
use Continuum\Entity\User;
use Continuum\Security\User\UserRole;
use Override;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, null|Exercise>
 */
final class ExerciseVoter extends Voter
{
    public const string VIEW = 'EXERCISE_VIEW';
    public const string CREATE = 'EXERCISE_CREATE';
    public const string EDIT = 'EXERCISE_EDIT';
    public const string DELETE = 'EXERCISE_DELETE';

    public function __construct(
        private readonly Security $security,
    ) {}

    #[Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::CREATE, self::EDIT, self::DELETE], strict: true);
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
            self::EDIT => $this->isEditGranted($user, $subject),
            self::DELETE => $this->isDeleteGranted($user, $subject),
            default => false,
        };
    }

    private function isEditGranted(User $user, mixed $subject): bool
    {
        if (!$subject instanceof Exercise) {
            return false;
        }

        return $this->security->isGrantedForUser($user, UserRole::Admin->value);
    }

    private function isDeleteGranted(User $user, mixed $subject): bool
    {
        if (!$subject instanceof Exercise) {
            return false;
        }

        return $subject->workoutExercises->isEmpty()
            && $this->security->isGrantedForUser($user, UserRole::Admin->value);
    }
}
