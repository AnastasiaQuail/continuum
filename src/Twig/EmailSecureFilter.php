<?php

declare(strict_types=1);

namespace Continuum\Twig;

use Twig\Attribute\AsTwigFilter;

final readonly class EmailSecureFilter
{
    #[AsTwigFilter('email_secure')]
    public function __invoke(string $email): string
    {
        // @phpstan-ignore offsetAccess.notFound
        [$name, $domain] = explode('@', $email);

        if (strlen($name) <= 2) {
            return $email;
        }

        $offset = strlen($name) <= 4 ? 1 : 2;

        return substr($name, 0, $offset)
            . str_repeat('*', strlen($name) - $offset * 2)
            . substr($name, -$offset)
            . '@' . $domain;
    }
}
