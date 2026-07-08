<?php

declare(strict_types=1);

namespace Continuum\Form;

use Continuum\Dto\Request\Reflection\EditWeeklyReflection;
use Continuum\Entity\WeeklyReflection;
use Continuum\Form\Type\AbstractImmutableType;
use Override;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractImmutableType<EditWeeklyReflection>
 */
final class EditWeeklyReflectionType extends AbstractImmutableType
{
    #[Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var array{weeklyReflection: null|WeeklyReflection} $options */
        $weeklyReflection = $options['weeklyReflection'];

        $builder->setDataMapper($this)
            ->add('joy', TextareaType::class, [
                'data' => $weeklyReflection?->joy->text,
                'attr' => [
                    'autofocus' => true,
                    'autocomplete' => 'off',
                    'rows' => 5,
                ],
            ])
            ->add('isJoyPrivate', CheckboxType::class, [
                'data' => $weeklyReflection?->joy->isPrivate,
                'label' => 'private?',
                'required' => false,
            ])
            ->add('difficulty', TextareaType::class, [
                'data' => $weeklyReflection?->difficulty->text,
                'attr' => [
                    'autocomplete' => 'off',
                    'rows' => 5,
                ],
            ])
            ->add('isDifficultyPrivate', CheckboxType::class, [
                'data' => $weeklyReflection?->difficulty->isPrivate,
                'label' => 'private?',
                'required' => false,
            ])
            ->add('achievement', TextareaType::class, [
                'data' => $weeklyReflection?->achievement->text,
                'attr' => [
                    'autocomplete' => 'off',
                    'rows' => 5,
                ],
            ])
            ->add('isAchievementPrivate', CheckboxType::class, [
                'data' => $weeklyReflection?->achievement->isPrivate,
                'label' => 'private?',
                'required' => false,
            ]);
    }

    /**
     * @param array{
     *  joy: FormInterface<null|string>,
     *  isJoyPrivate: FormInterface<bool>,
     *  difficulty: FormInterface<null|string>,
     *  isDifficultyPrivate: FormInterface<bool>,
     *  achievement: FormInterface<null|string>,
     *  isAchievementPrivate: FormInterface<bool>
     * } $forms
     *
     * @phpstan-ignore method.childParameterType (fix of parent stub)
     */
    #[Override]
    protected function mapDataClass(array $forms): EditWeeklyReflection
    {
        return new EditWeeklyReflection(
            joy: $forms['joy']->getData() ?? '',
            isJoyPrivate: $forms['isJoyPrivate']->getData(),
            difficulty: $forms['difficulty']->getData() ?? '',
            isDifficultyPrivate: $forms['isDifficultyPrivate']->getData(),
            achievement: $forms['achievement']->getData() ?? '',
            isAchievementPrivate: $forms['isAchievementPrivate']->getData(),
        );
    }

    #[Override]
    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault('weeklyReflection', value: null);
        $resolver->setAllowedTypes('weeklyReflection', ['null', WeeklyReflection::class]);
    }
}
