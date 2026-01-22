<?php

declare(strict_types=1);

namespace Continuum\Form\Type;

use ReflectionMethod;
use ReflectionNamedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Traversable;

/**
 * @template T of object
 */
abstract class AbstractImmutableType extends AbstractType implements DataMapperInterface
{
    /**
     * @var class-string<T>
     */
    private string $dataClass = '';

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => $this->getDataClass(),
            'empty_data' => null,
        ]);
    }

    /**
     * @return class-string<T>
     */
    private function getDataClass(): string
    {
        if ($this->dataClass !== '') {
            return $this->dataClass;
        }

        $method = new ReflectionMethod(static::class, 'mapDataClass');
        $returnType = $method->getReturnType();

        if (!$returnType instanceof ReflectionNamedType) {
            throw new InvalidConfigurationException('You must define a valid return type.');
        }

        return $this->dataClass = $returnType->getName();
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

    /**
     * @param T|null $viewData
     */
    public function mapDataToForms(mixed $viewData, Traversable $forms): void
    {
        if (null === $viewData) {
            return;
        }

        $dataClass = $this->getDataClass();

        if (!$viewData instanceof $dataClass) {
            throw new UnexpectedTypeException($viewData, $dataClass);
        }

        /** @var FormInterface[] $data */
        $data = iterator_to_array($forms);

        $this->mapForms($data, $viewData);
    }

    /**
     * @param FormInterface[] $forms
     * @param T $dataClass
     */
    protected function mapForms(array $forms, object $dataClass): void
    {
        throw new InvalidConfigurationException('This method should be overridden.');
    }

    public function mapFormsToData(Traversable $forms, mixed &$viewData): void
    {
        /** @var FormInterface[] $data */
        $data = iterator_to_array($forms);

        $viewData = $this->mapDataClass($data);
    }

    /**
     * @param FormInterface[] $forms
     *
     * @return T
     */
    abstract protected function mapDataClass(array $forms): object;
}
