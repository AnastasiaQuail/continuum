<?php

declare(strict_types=1);

namespace Continuum\Form;

use Continuum\Dto\Request\Reflection\EditMoodReflection;
use Continuum\Entity\MoodReflection;
use Continuum\Enum\MoodType;
use Continuum\Form\Type\AbstractImmutableType;
use Override;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
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
            ->add('type', EnumType::class, [
                'class' => MoodType::class,
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

    #[Override]
    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault('moodReflection', null);
        $resolver->setAllowedTypes('moodReflection', ['null', MoodReflection::class]);
    }
}
