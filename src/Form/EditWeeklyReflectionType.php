<?php

declare(strict_types=1);

namespace Continuum\Form;

use Continuum\Dto\Request\Reflection\EditWeeklyReflection;
use Continuum\Dto\Request\TextField;
use Continuum\Entity\WeeklyReflection;
use Continuum\Form\Type\AbstractImmutableType;
use Continuum\Form\Type\TextFieldType;
use Override;
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
        $reflection = $options['weeklyReflection'];

        $builder->setDataMapper($this)
            ->add('joy', TextFieldType::class, [
                'data' => null !== $reflection ? TextField::create($reflection->joy) : null,
                'label' => 'Joy',
            ])
            ->add('difficulty', TextFieldType::class, [
                'data' => null !== $reflection ? TextField::create($reflection->difficulty) : null,
                'label' => 'Difficulty',
            ])
            ->add('achievement', TextFieldType::class, [
                'data' => null !== $reflection ? TextField::create($reflection->achievement) : null,
                'label' => 'Achievement',
            ]);
    }

    /**
     * @param array{
     *  joy: FormInterface<TextField>,
     *  difficulty: FormInterface<TextField>,
     *  achievement: FormInterface<TextField>,
     * } $forms
     *
     * @phpstan-ignore method.childParameterType (fix of parent stub)
     */
    #[Override]
    protected function mapDataClass(array $forms): EditWeeklyReflection
    {
        return new EditWeeklyReflection(
            joy: $forms['joy']->getData(),
            difficulty: $forms['difficulty']->getData(),
            achievement: $forms['achievement']->getData(),
        );
    }

    #[Override]
    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault('weeklyReflection', value: null);
        $resolver->setAllowedTypes('weeklyReflection', ['null', WeeklyReflection::class]);
    }
}
