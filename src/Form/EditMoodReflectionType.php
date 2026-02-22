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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractImmutableType<EditMoodReflection>
 */
final class EditMoodReflectionType extends AbstractImmutableType
{
    #[Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var array{moodReflection: null|MoodReflection} $options */
        $moodReflection = $options['moodReflection'];

        $builder->setDataMapper($this)
            ->add('type', EnumType::class, [
                'class' => MoodType::class,
                'data' => null !== $moodReflection ? $moodReflection->type : MoodType::Okay,
            ])
            ->add('text', null, [
                'data' => null !== $moodReflection ? $moodReflection->text : '',
                'attr' => [
                    'autocomplete' => 'off',
                ],
            ]);
    }

    /**
     * @param array{
     *  type: FormInterface<MoodType>,
     *  text: FormInterface<null|string>
     * } $forms
     *
     * @phpstan-ignore method.childParameterType
     */
    #[Override]
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
