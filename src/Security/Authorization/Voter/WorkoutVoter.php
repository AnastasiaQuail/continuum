<?php

declare(strict_types=1);

namespace Continuum\Security\Authorization\Voter;

use Continuum\Entity\User;
use Continuum\Entity\Workout;
use Continuum\Entity\WorkoutExercise;
use Continuum\Entity\WorkoutSet;
use Continuum\Security\User\UserRole;
use DateTimeImmutable;
use Override;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, null|Workout|WorkoutExercise|WorkoutSet>
 */
final class WorkoutVoter extends Voter
{
    public const string VIEW = 'WORKOUT_VIEW';
    public const string REPORT_VIEW = 'WORKOUT_REPORT_VIEW';
    public const string CREATE = 'WORKOUT_CREATE';
    public const string EDIT = 'WORKOUT_EDIT';
    public const string DELETE = 'WORKOUT_DELETE';
    public const string EXERCISE_CREATE = 'WORKOUT_EXERCISE_CREATE';
    public const string EXERCISE_DELETE = 'WORKOUT_EXERCISE_DELETE';
    public const string SET_CREATE = 'WORKOUT_SET_CREATE';
    public const string SET_DELETE = 'WORKOUT_SET_DELETE';

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
                self::CREATE,
                self::EDIT,
                self::DELETE,
                self::EXERCISE_CREATE,
                self::EXERCISE_DELETE,
                self::SET_CREATE,
                self::SET_DELETE,
            ],
            true
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
            self::VIEW,
            self::REPORT_VIEW => true,

            self::CREATE => $this->security->isGrantedForUser($user, UserRole::Admin->value),

            self::EDIT,
            self::EXERCISE_CREATE,
            self::SET_CREATE,
            self::DELETE,
            self::EXERCISE_DELETE,
            self::SET_DELETE => (
                $this->editWorkout($user, $attribute, $subject)
                    && $this->security->isGrantedForUser($user, UserRole::Admin->value)
            )
                || $this->security->isGrantedForUser($user, UserRole::SuperAdmin->value),
            default => false,
        };
    }

    private function editWorkout(User $user, string $attribute, mixed $subject): bool
    {
        if (!$this->security->isGrantedForUser($user, UserRole::Admin->value)) {
            return false;
        }

        return match ($attribute) {
            self::EDIT,
            self::DELETE,
            self::EXERCISE_CREATE => $subject instanceof Workout && $this->isCurrentDay($subject),
            self::EXERCISE_DELETE,
            self::SET_CREATE => $subject instanceof WorkoutExercise && $this->isCurrentDay($subject->getWorkout()),
            self::SET_DELETE => $subject instanceof WorkoutSet
                && $this->isCurrentDay($subject->getWorkoutExercise()->getWorkout()),
            default => false,
        };
    }

    private function isCurrentDay(Workout $workout): bool
    {
        return $workout->getDate()->format('Y-m-d') === new DateTimeImmutable()->format('Y-m-d');
    }
}
