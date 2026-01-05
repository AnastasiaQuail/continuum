<?php

declare(strict_types=1);

namespace Continuum\Security\User;

enum UserStatus: int
{
    case Created = 0;
    case Active = 1;
    case Disabled = 2;
}
