<?php

declare(strict_types=1);

namespace Continuum\Form;

use Continuum\Dto\Request\Workout\EditExercise;
use Continuum\Entity\Exercise;
use Continuum\Enum\ExerciseGroup;
use Continuum\Form\Type\AbstractImmutableType;
use Override;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractImmutableType<EditExercise>
 */
final class EditExerciseType extends AbstractImmutableType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var array{exercise: null|Exercise} $options */
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

    /**
     * @param array{
     *  group: FormInterface<null|ExerciseGroup>,
     *  name: FormInterface<null|string>
     * } $forms
     *
     * @phpstan-ignore method.childParameterType
     */
    protected function mapDataClass(array $forms): EditExercise
    {
        return new EditExercise(
            $forms['group']->getData() ?? ExerciseGroup::Arms,
            $forms['name']->getData() ?? '',
        );
    }

    #[Override]
    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault('exercise', null);
        $resolver->setAllowedTypes('exercise', ['null', Exercise::class]);
    }
}
