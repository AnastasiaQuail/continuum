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
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EditWeeklyReflectionType extends AbstractImmutableType
{
    /**
     * @param array{weeklyReflection: null|WeeklyReflection} $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $weeklyReflection = $options['weeklyReflection'];

        $builder->setDataMapper($this)
            ->add('joy', TextareaType::class, [
                'data' => $weeklyReflection?->joy->text,
                'attr' => [
                    'autofocus' => true,
                    'autocomplete' => 'off',
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
                ],
            ])
            ->add('isAchievementPrivate', CheckboxType::class, [
                'data' => $weeklyReflection?->achievement->isPrivate,
                'label' => 'private?',
                'required' => false,
            ]);
    }

    protected function mapDataClass(array $forms): EditWeeklyReflection
    {
        return new EditWeeklyReflection(
            $forms['joy']->getData() ?? '',
            $forms['isJoyPrivate']->getData(),
            $forms['difficulty']->getData() ?? '',
            $forms['isDifficultyPrivate']->getData(),
            $forms['achievement']->getData() ?? '',
            $forms['isAchievementPrivate']->getData(),
        );
    }

    #[Override]
    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault('weeklyReflection', null);
        $resolver->setAllowedTypes('weeklyReflection', ['null', WeeklyReflection::class]);
    }
}
