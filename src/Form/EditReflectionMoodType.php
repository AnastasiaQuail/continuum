<?php

declare(strict_types=1);

namespace Continuum\Form;

use Continuum\Dto\Request\Reflection\EditReflectionMood;
use Continuum\Entity\ReflectionMood;
use Continuum\Enum\MoodType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EditReflectionMoodType extends AbstractImmutableType
{
    /**
     * @param array{mood: null|ReflectionMood} $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $reflectionMood = $options['mood'];

        $builder->setDataMapper($this)
            ->add('type', null, [
                'data' => $reflectionMood?->getType() ?? MoodType::Okay,
            ])
            ->add('text', null, [
                'data' => $reflectionMood?->getText() ?? '',
            ]);
    }

    protected function mapDataClass(array $forms): EditReflectionMood
    {
        return new EditReflectionMood(
            $forms['type']->getData(),
            $forms['text']->getData() ?? '',
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('mood', null);
        $resolver->setAllowedTypes('mood', ['null', ReflectionMood::class]);
    }
}
