<?php

declare(strict_types=1);

namespace Continuum\Security\User;

enum UserRole: string
{
    case User = 'ROLE_USER';
    case Admin = 'ROLE_ADMIN';
    case SuperAdmin = 'ROLE_SUPER_ADMIN';
}
