<?php

declare(strict_types=1);

namespace Continuum\Form;

use Continuum\Dto\Request\Measurement\EditMeasurement;
use Continuum\Dto\Response\Measurement\LastMeasurement;
use Continuum\Entity\BodyMeasurement;
use Continuum\Entity\User;
use Continuum\Form\Type\AbstractImmutableType;
use Continuum\Form\Type\MeasurementType;
use DateTimeImmutable;
use Override;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @extends AbstractImmutableType<EditMeasurement>
 */
final class EditMeasurementType extends AbstractImmutableType
{
    public function __construct(
        private readonly Security $security,
    ) {}

    #[Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var array{lastMeasurement: null|LastMeasurement, measurement: null|BodyMeasurement} $options */
        $lastMeasurement = $options['lastMeasurement'];
        $measurement = $options['measurement'];
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new LogicException('User must be authenticated.');
        }

        $datetime = null !== $measurement ? $measurement->datetime : new DateTimeImmutable('now');

        $builder->setDataMapper($this)
            ->add('datetime', DateTimeType::class, [
                'data' => $datetime->setTimezone($user->timezone),
                'model_timezone' => $user->timezone->getName(),
                'view_timezone' => $user->timezone->getName(),
                'input' => 'datetime_immutable',
                'constraints' => [
                    new Callback(
                        static function (DateTimeImmutable $datetime, ExecutionContextInterface $context) use (
                            $user
                        ): void {
                            $currentDate = new DateTimeImmutable('now', $user->timezone);

                            if ($currentDate->format('Y-m-d') < $datetime->format('Y-m-d')) {
                                $context->buildViolation('You cannot manage measurements for future days.')
                                    ->addViolation();
                            }
                        }
                    ),
                ],
            ])
            ->add('weight', MeasurementType::class, [
                'data' => $measurement?->weight,
                'data-prev' => $lastMeasurement?->weight,
                'help' => $this->getHelp($lastMeasurement?->weight, 'kg'),
                'min' => EditMeasurement::WEIGHT_MIN,
                'max' => EditMeasurement::WEIGHT_MAX,
                'postfix' => 'kg',
                'attr' => [
                    'autofocus' => true,
                    'step' => 0.1,
                ],
            ])
            ->add('neck', MeasurementType::class, [
                'data' => $measurement?->neck,
                'data-prev' => $lastMeasurement?->neck,
                'help' => $this->getHelp($lastMeasurement?->neck),
                'min' => EditMeasurement::NECK_MIN,
                'max' => EditMeasurement::NECK_MAX,
                'postfix' => 'cm',
                'required' => false,
            ])
            ->add('chest', MeasurementType::class, [
                'data' => $measurement?->chest,
                'data-prev' => $lastMeasurement?->chest,
                'help' => $this->getHelp($lastMeasurement?->chest),
                'min' => EditMeasurement::CHEST_MIN,
                'max' => EditMeasurement::CHEST_MAX,
                'postfix' => 'cm',
                'required' => false,
            ])
            ->add('shoulders', MeasurementType::class, [
                'data' => $measurement?->shoulders,
                'data-prev' => $lastMeasurement?->shoulders,
                'help' => $this->getHelp($lastMeasurement?->shoulders),
                'min' => EditMeasurement::SHOULDERS_MIN,
                'max' => EditMeasurement::SHOULDERS_MAX,
                'postfix' => 'cm',
                'required' => false,
            ])
            ->add('waist', MeasurementType::class, [
                'data' => $measurement?->waist,
                'data-prev' => $lastMeasurement?->waist,
                'help' => $this->getHelp($lastMeasurement?->waist),
                'min' => EditMeasurement::WAIST_MIN,
                'max' => EditMeasurement::WAIST_MAX,
                'postfix' => 'cm',
                'required' => false,
            ])
            ->add('flexedBiceps', MeasurementType::class, [
                'data' => $measurement?->flexedBiceps,
                'data-prev' => $lastMeasurement?->flexedBiceps,
                'help' => $this->getHelp($lastMeasurement?->flexedBiceps),
                'min' => EditMeasurement::BICEPS_MIN,
                'max' => EditMeasurement::BICEPS_MAX,
                'postfix' => 'cm',
                'required' => false,
            ])
            ->add('hips', MeasurementType::class, [
                'data' => $measurement?->hips,
                'data-prev' => $lastMeasurement?->hips,
                'help' => $this->getHelp($lastMeasurement?->hips),
                'min' => EditMeasurement::HIPS_MIN,
                'max' => EditMeasurement::HIPS_MAX,
                'postfix' => 'cm',
                'required' => false,
            ])
            ->add('thigh', MeasurementType::class, [
                'data' => $measurement?->thigh,
                'data-prev' => $lastMeasurement?->thigh,
                'help' => $this->getHelp($lastMeasurement?->thigh),
                'min' => EditMeasurement::THIGH_MIN,
                'max' => EditMeasurement::THIGH_MAX,
                'postfix' => 'cm',
                'required' => false,
            ])
            ->add('calf', MeasurementType::class, [
                'data' => $measurement?->calf,
                'data-prev' => $lastMeasurement?->calf,
                'help' => $this->getHelp($lastMeasurement?->calf),
                'min' => EditMeasurement::CALF_MIN,
                'max' => EditMeasurement::CALF_MAX,
                'postfix' => 'cm',
                'required' => false,
            ]);
    }

    /**
     * @param array{
     *  datetime: FormInterface<DateTimeImmutable>,
     *  weight: FormInterface<float>,
     *  neck: FormInterface<null|float>,
     *  chest: FormInterface<null|float>,
     *  shoulders: FormInterface<null|float>,
     *  waist: FormInterface<null|float>,
     *  flexedBiceps: FormInterface<null|float>,
     *  hips: FormInterface<null|float>,
     *  thigh: FormInterface<null|float>,
     *  calf: FormInterface<null|float>
     * } $forms
     *
     * @phpstan-ignore method.childParameterType (fix of parent stub)
     */
    #[Override]
    protected function mapDataClass(array $forms): EditMeasurement
    {
        return new EditMeasurement(
            datetime: $forms['datetime']->getData(),
            weight: $forms['weight']->getData(),
            neck: $forms['neck']->getData(),
            chest: $forms['chest']->getData(),
            shoulders: $forms['shoulders']->getData(),
            waist: $forms['waist']->getData(),
            flexedBiceps: $forms['flexedBiceps']->getData(),
            hips: $forms['hips']->getData(),
            thigh: $forms['thigh']->getData(),
            calf: $forms['calf']->getData(),
        );
    }

    #[Override]
    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault('lastMeasurement', value: null);
        $resolver->setDefault('measurement', value: null);
        $resolver->setAllowedTypes('lastMeasurement', ['null', LastMeasurement::class]);
        $resolver->setAllowedTypes('measurement', ['null', BodyMeasurement::class]);
    }

    private function getHelp(float|int|null $value, string $name = 'cm'): ?string
    {
        if (null === $value) {
            return null;
        }

        if (is_int($value) || 0.0 === fmod($value, 1.0)) {
            return sprintf('Last: %s %s', $value, $name);
        }

        return sprintf('Last: %.1f %s', $value, $name);
    }
}
