<?php

declare (strict_types=1);

namespace Continuum\Form;

use Continuum\Dto\Request\Reflection\EditWeeklyReflection;
use Continuum\Entity\WeeklyReflection;
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
                'attr' => ['autofocus' => true],
                'data' => $weeklyReflection?->getJoy(),
            ])
            ->add('isJoyPrivate', CheckboxType::class, [
                'data' => $weeklyReflection?->isJoyPrivate(),
                'label' => 'private?',
                'required' => false,
            ])
            ->add('difficulty', TextareaType::class, [
                'data' => $weeklyReflection?->getDifficulty(),
            ])
            ->add('isDifficultyPrivate', CheckboxType::class, [
                'data' => $weeklyReflection?->isDifficultyPrivate(),
                'label' => 'private?',
                'required' => false,
            ])
            ->add('achievement', TextareaType::class, [
                'data' => $weeklyReflection?->getAchievement(),
            ])
            ->add('isAchievementPrivate', CheckboxType::class, [
                'data' => $weeklyReflection?->isAchievementPrivate(),
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('weeklyReflection', null);
        $resolver->setAllowedTypes('weeklyReflection', ['null', WeeklyReflection::class]);
    }
}
