<?php

declare(strict_types=1);

namespace Continuum\Form;

use Continuum\Dto\Request\User\EditLocation;
use Continuum\Entity\Location;
use Continuum\Form\Type\AbstractImmutableType;
use Continuum\Form\Type\MeasurementType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EditLocationType extends AbstractImmutableType
{
    /**
     * @param array{location: null|Location} $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $location = $options['location'];

        $builder->setDataMapper($this)
            ->add('latitude', MeasurementType::class, [
                'data' => $location?->getLatitude(),
                'min' => EditLocation::LATITUDE_MIN,
                'max' => EditLocation::LATITUDE_MAX,
                'scale' => 6,
                'attr' => [
                    'step' => 0.000001,
                    'autocomplete' => 'off',
                ],
            ])
            ->add('longitude', MeasurementType::class, [
                'data' => $location?->getLongitude(),
                'min' => EditLocation::LONGITUDE_MIN,
                'max' => EditLocation::LONGITUDE_MAX,
                'scale' => 6,
                'attr' => [
                    'step' => 0.000001,
                    'autocomplete' => 'off',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('location', null);
        $resolver->setAllowedTypes('location', ['null', Location::class]);
    }

    protected function mapDataClass(array $forms): EditLocation
    {
        return new EditLocation(
            latitude: $forms['latitude']->getData(),
            longitude: $forms['longitude']->getData(),
        );
    }
}
