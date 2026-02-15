<?php

declare(strict_types=1);

namespace Continuum\Twig;

use Continuum\Entity\User;
use DateTimeImmutable;
use LogicException;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Attribute\AsTwigFunction;

final readonly class UserDateFunction
{
    public function __construct(
        private Security $security,
    ) {}

    #[AsTwigFunction('user_date')]
    public function __invoke(string $datetime = 'now'): DateTimeImmutable
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new LogicException('User must be authenticated.');
        }

        return new DateTimeImmutable($datetime, $user->timezone);
    }
}
