<?php

declare(strict_types=1);

namespace Continuum\Form\Type;

use Override;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MeasurementType extends NumberType
{
    #[Override]
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (!isset($view->vars['attr']['step'])) {
            $view->vars['attr']['step'] = 0.5;
        }
        if (!isset($view->vars['attr']['autocomplete'])) {
            $view->vars['attr']['autocomplete'] = 'off';
        }

        parent::buildView($view, $form, $options);

        if (null !== $options['min']) {
            $view->vars['attr']['min'] = $options['min'];
        }
        if (null !== $options['max']) {
            $view->vars['attr']['max'] = $options['max'];
        }
        if (null !== $options['postfix']) {
            $view->vars['attr']['data-mask'] = 'postfix';
            $view->vars['attr']['data-postfix'] = $options['postfix'];
        }
    }

    #[Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('html5', true);
        $resolver->setDefault('min', null);
        $resolver->setDefault('max', null);
        $resolver->setDefault('postfix', null);

        $resolver->setAllowedTypes('min', ['null', 'int']);
        $resolver->setAllowedTypes('max', ['null', 'int']);
        $resolver->setAllowedTypes('postfix', ['null', 'string']);
    }
}
