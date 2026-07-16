<?php

declare(strict_types=1);

namespace Continuum\Twig\Form;

use Twig\Attribute\AsTwigFunction;
use Twig\Environment;

final readonly class FormRowSubmitFunction
{
    public function __construct(
        private Environment $twig,
    ) {}

    #[AsTwigFunction('form_row_submit', isSafe: ['html'])]
    public function __invoke(string $label = 'Save'): string
    {
        return $this->twig->render('form/_form_row_submit.html.twig', [
            'label' => $label,
        ]);
    }
}
