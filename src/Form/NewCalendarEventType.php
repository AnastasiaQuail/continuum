<?php

declare(strict_types=1);

namespace Continuum\Form;

use Continuum\Dto\Request\Calendar\NewCalendarEvent;
use Continuum\Enum\CalendarEventType;
use DateTimeZone;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class NewCalendarEventType extends AbstractImmutableType
{
    /**
     * @param array{timezone: null|DateTimeZone} $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setDataMapper($this)
            ->add('title', null, [
                'attr' => ['autofocus' => true],
            ])
            ->add('type', null, [
                'data' => CalendarEventType::Blue,
            ])
            ->add('time', TimeType::class, [
                'input' => 'datetime_immutable',
                'model_timezone' => $options['timezone']?->getName(),
                'required' => false,
            ]);
    }

    protected function mapDataClass(array $forms): NewCalendarEvent
    {
        $time = $forms['time']->getData();

        return new NewCalendarEvent(
            $forms['title']->getData() ?? '',
            $forms['type']->getData(),
            $time !== '' ? $time : null,
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('timezone', null);
        $resolver->setAllowedTypes('timezone', ['null', DateTimeZone::class]);
    }
}
