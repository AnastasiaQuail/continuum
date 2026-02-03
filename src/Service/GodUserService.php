<?php

declare(strict_types=1);

namespace Continuum\Service;

use Continuum\Entity\User;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class GodUserService
{
    public function __construct(
        #[Autowire(env: 'APP_USER_BIRTH_DATE')]
        private string $userBirthDate,
        #[Autowire(env: 'int:APP_USER_HEIGHT')]
        private int $userHeight,
    ) {}

    public function getAge(User $user, DateTimeImmutable $datetime): int
    {
        $birthday = new DateTimeImmutable($this->userBirthDate, $user->getTimezone())->setTime(0, 0);
        $date = $datetime->setTimezone($user->getTimezone());

        return $birthday->diff($date)->y;
    }

    public function getHeight(): int
    {
        return $this->userHeight;
    }
}
