<?php

declare(strict_types=1);

namespace Continuum\Form\Type;

use Continuum\Dto\Request\TextField;
use Override;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @extends AbstractImmutableType<TextField>
 */
final class TextFieldType extends AbstractImmutableType
{
    #[Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var array{data: null|TextField, label: null|string} $options */
        $textField = $options['data'];

        $builder->setDataMapper($this)
            ->add('text', TextareaType::class, [
                'data' => $textField?->text,
                'label' => $options['label'],
                'attr' => [
                    'autocomplete' => 'off',
                ],
            ])
            ->add('isPrivate', CheckboxType::class, [
                'data' => $textField?->isPrivate,
                'label' => 'Secure',
                'required' => false,
            ]);
    }

    /**
     * @param array{
     *  text: FormInterface<null|string>,
     *  isPrivate: FormInterface<bool>,
     * } $forms
     *
     * @phpstan-ignore method.childParameterType (fix of parent stub)
     */
    #[Override]
    protected function mapDataClass(array $forms): TextField
    {
        return new TextField(
            text: $forms['text']->getData() ?? '',
            isPrivate: $forms['isPrivate']->getData(),
        );
    }

    /**
     * @param array{
     *   text: FormInterface<null|string>,
     *   isPrivate: FormInterface<bool>,
     *  } $forms
     * @param TextField $dataClass
     *
     * @phpstan-ignore method.childParameterType (fix of parent stub)
     */
    #[Override]
    protected function mapForms(array $forms, object $dataClass): void
    {
        $forms['text']->setData($dataClass->text);
        $forms['isPrivate']->setData($dataClass->isPrivate);
    }
}
