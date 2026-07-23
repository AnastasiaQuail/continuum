<?php

declare(strict_types=1);

namespace Continuum\Twig\Form;

use Twig\Attribute\AsTwigFunction;
use Twig\Environment;

final readonly class FormDeleteFunction
{
    public function __construct(
        private Environment $twig,
    ) {}

    #[AsTwigFunction('form_delete', isSafe: ['html'])]
    public function __invoke(string $action, bool $isGranted = true, string $label = 'Delete'): string
    {
        return $this->twig->render('form/_form_delete.html.twig', [
            'action' => $action,
            'is_form_granted' => $isGranted,
            'label' => $label,
        ]);
    }
}
