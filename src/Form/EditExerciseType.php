<?php

declare(strict_types=1);

namespace Continuum\Form;

use Continuum\Dto\Request\Workout\EditExercise;
use Continuum\Entity\Exercise;
use Continuum\Enum\ExerciseGroup;
use Continuum\Form\Type\AbstractImmutableType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EditExerciseType extends AbstractImmutableType
{
    /**
     * @param array{exercise: null|Exercise} $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $exercise = $options['exercise'];

        $builder->setDataMapper($this)
            ->add('group', EnumType::class, [
                'class' => ExerciseGroup::class,
                'data' => $exercise?->getGroup(),
            ])
            ->add('name', TextType::class, [
                'data' => $exercise?->getName(),
                'attr' => [
                    'autocomplete' => 'off',
                ],
            ]);
    }

    protected function mapDataClass(array $forms): EditExercise
    {
        return new EditExercise(
            $forms['group']->getData() ?? ExerciseGroup::Arms,
            $forms['name']->getData() ?? '',
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('exercise', null);
        $resolver->setAllowedTypes('exercise', ['null', Exercise::class]);
    }
}
