<?php

declare(strict_types=1);

namespace Continuum\Form;

use Continuum\Dto\Request\Calendar\NewCalendarEvent;
use Continuum\Enum\CalendarEventType;
use Continuum\Form\Type\AbstractImmutableType;
use DateTimeImmutable;
use DateTimeZone;
use Override;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractImmutableType<NewCalendarEvent>
 */
final class NewCalendarEventType extends AbstractImmutableType
{
    #[Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var array{timezone: null|DateTimeZone} $options */
        $builder->setDataMapper($this)
            ->add('title', null, [
                'attr' => ['autofocus' => true],
            ])
            ->add('type', EnumType::class, [
                'class' => CalendarEventType::class,
                'data' => CalendarEventType::Blue,
            ])
            ->add('time', TimeType::class, [
                'input' => 'datetime_immutable',
                'model_timezone' => $options['timezone']?->getName(),
                'required' => false,
            ]);
    }

    /**
     * @param array{
     *  title: FormInterface<null|string>,
     *  type: FormInterface<CalendarEventType>,
     *  time: FormInterface<DateTimeImmutable|string>
     * } $forms
     *
     * @phpstan-ignore method.childParameterType (fix of parent stub)
     */
    #[Override]
    protected function mapDataClass(array $forms): NewCalendarEvent
    {
        $time = $forms['time']->getData();

        return new NewCalendarEvent(
            $forms['title']->getData() ?? '',
            $forms['type']->getData(),
            ($time instanceof DateTimeImmutable) ? $time : null,
        );
    }

    #[Override]
    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault('timezone', null);
        $resolver->setAllowedTypes('timezone', ['null', DateTimeZone::class]);
    }
}
