<?php

declare(strict_types=1);

namespace Continuum\Form\Type;

use Override;
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
 *
 * @extends AbstractType<T>
 */
abstract class AbstractImmutableType extends AbstractType implements DataMapperInterface
{
    /**
     * @var null|class-string<T>
     */
    private ?string $dataClass = null;

    #[Override]
    final public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => $this->getDataClass(),
            'empty_data' => null,
        ]);

        $this->configure($resolver);
    }

    #[Override]
    final public function getBlockPrefix(): string
    {
        return '';
    }

    /**
     * @param null|T $viewData
     * @param Traversable<mixed, FormInterface<mixed>> $forms
     *
     * @phpstan-ignore method.childParameterType (fix of parent stub), method.childParameterType (fix of parent stub)
     */
    #[Override]
    final public function mapDataToForms(mixed $viewData, Traversable $forms): void
    {
        if (null === $viewData) {
            return;
        }

        $dataClass = $this->getDataClass();

        if (!$viewData instanceof $dataClass) {
            throw new UnexpectedTypeException($viewData, $dataClass);
        }

        /** @var list<FormInterface<mixed>> $data */
        $data = iterator_to_array($forms);

        $this->mapForms($data, $viewData);
    }

    /**
     * @param Traversable<mixed, FormInterface<mixed>> $forms
     *
     * @phpstan-ignore method.childParameterType (fix of parent stub)
     */
    #[Override]
    final public function mapFormsToData(Traversable $forms, mixed &$viewData): void
    {
        /** @var array<string, FormInterface<mixed>> $data */
        $data = iterator_to_array($forms);

        $viewData = $this->mapDataClass($data);
    }

    protected function configure(OptionsResolver $resolver): void
    {
        // Override this method for configure options
    }

    /**
     * @param list<FormInterface<mixed>> $forms
     * @param T $dataClass
     */
    protected function mapForms(array $forms, object $dataClass): void
    {
        throw new InvalidConfigurationException('This method should be overridden.');
    }

    /**
     * @param array<string, FormInterface<mixed>> $forms
     *
     * @return T
     */
    abstract protected function mapDataClass(array $forms): object;

    /**
     * @return class-string<T>
     */
    private function getDataClass(): string
    {
        if (null !== $this->dataClass) {
            return $this->dataClass;
        }

        $method = new ReflectionMethod(static::class, 'mapDataClass');
        $returnType = $method->getReturnType();

        if (!$returnType instanceof ReflectionNamedType) {
            throw new InvalidConfigurationException('You must define a valid return type.');
        }

        /** @var class-string<T> $className */
        $className = $returnType->getName();

        if (!class_exists($className)) {
            throw new InvalidConfigurationException(sprintf('Class "%s" does not exist.', $className));
        }

        return $this->dataClass = $className;
    }
}
