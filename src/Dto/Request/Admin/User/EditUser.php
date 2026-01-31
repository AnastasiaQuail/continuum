<?php

declare(strict_types=1);

namespace Continuum\Dto\Request\Admin\User;

use Continuum\Security\User\UserRole;
use Continuum\Security\User\UserStatus;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class EditUser
{
    public function __construct(
        public UserStatus $status,
        /**
         * @var list<non-empty-string>
         */
        #[Assert\All([
            new Assert\Choice(callback: [self::class, 'values']),
        ])]
        public array $roles = [],
    ) {}

    /**
     * @return list<non-empty-string>
     */
    public static function values(): array
    {
        return array_column(UserRole::cases(), 'value');
    }
}
