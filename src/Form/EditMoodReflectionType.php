<?php

declare(strict_types=1);

namespace Continuum\Form;

use Continuum\Dto\Request\Reflection\EditMoodReflection;
use Continuum\Entity\MoodReflection;
use Continuum\Enum\MoodType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EditMoodReflectionType extends AbstractImmutableType
{
    /**
     * @param array{moodReflection: null|MoodReflection} $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $moodReflection = $options['moodReflection'];

        $builder->setDataMapper($this)
            ->add('type', null, [
                'data' => $moodReflection?->getType() ?? MoodType::Okay,
            ])
            ->add('text', null, [
                'data' => $moodReflection?->getText() ?? '',
                'attr' => [
                    'autocomplete' => 'off',
                ],
            ]);
    }

    protected function mapDataClass(array $forms): EditMoodReflection
    {
        return new EditMoodReflection(
            $forms['type']->getData(),
            $forms['text']->getData() ?? '',
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('moodReflection', null);
        $resolver->setAllowedTypes('moodReflection', ['null', MoodReflection::class]);
    }
}
