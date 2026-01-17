<?php

declare(strict_types=1);

namespace Continuum\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MeasurementType extends NumberType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (!isset($view->vars['attr']['step'])) {
            $view->vars['attr']['step'] = 0.5;
        }
        if (!isset($view->vars['attr']['autocomplete'])) {
            $view->vars['attr']['autocomplete'] = 'off';
        }

        parent::buildView($view, $form, $options);

        if ($options['min'] !== null) {
            $view->vars['attr']['min'] = $options['min'];
        }
        if ($options['max'] !== null) {
            $view->vars['attr']['max'] = $options['max'];
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('html5', true);
        $resolver->setDefault('min', null);
        $resolver->setDefault('max', null);

        $resolver->setAllowedTypes('min', ['null', 'int']);
        $resolver->setAllowedTypes('max', ['null', 'int']);
    }
}
