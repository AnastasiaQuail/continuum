<?php

declare(strict_types=1);

namespace Continuum\Form;

use Continuum\Dto\Request\User\EditLocation;
use Continuum\Entity\Location;
use Continuum\Form\Type\AbstractImmutableType;
use Continuum\Form\Type\MeasurementType;
use Override;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractImmutableType<EditLocation>
 */
final class EditLocationType extends AbstractImmutableType
{
    #[Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var array{location: null|Location} $options */
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

    #[Override]
    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault('location', null);
        $resolver->setAllowedTypes('location', ['null', Location::class]);
    }

    /**
     * @param array{
     *  latitude: FormInterface<float>,
     *  longitude: FormInterface<float>
     * } $forms
     *
     * @phpstan-ignore method.childParameterType
     */
    #[Override]
    protected function mapDataClass(array $forms): EditLocation
    {
        return new EditLocation(
            latitude: $forms['latitude']->getData(),
            longitude: $forms['longitude']->getData(),
        );
    }
}
