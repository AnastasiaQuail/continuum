<?php

declare(strict_types=1);

namespace Continuum\Form;

use Continuum\Dto\Request\Measurement\EditMeasurement;
use Continuum\Dto\Response\Measurement\LastMeasurement;
use Continuum\Entity\BodyMeasurement;
use Continuum\Entity\User;
use Continuum\Form\Type\AbstractImmutableType;
use Continuum\Form\Type\MeasurementType;
use Continuum\Security\Authorization\Voter\FutureMonthVoter;
use DateTimeImmutable;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class EditMeasurementType extends AbstractImmutableType
{
    public function __construct(
        private readonly Security $security,
    ) {}

    /**
     * @param array{lastMeasurement: null|LastMeasurement, measurement: null|BodyMeasurement} $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $lastMeasurement = $options['lastMeasurement'];
        $measurement = $options['measurement'];
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new LogicException('User must be authenticated.');
        }

        $builder->setDataMapper($this)
            ->add('datetime', DateTimeType::class, [
                'data' => ($measurement?->getDatetime() ?? new DateTimeImmutable('now'))
                    ->setTimezone($user->getTimezone()),
                'model_timezone' => $user->getTimezone()->getName(),
                'view_timezone' => $user->getTimezone()->getName(),
                'input' => 'datetime_immutable',
                'constraints' => [
                    new Callback(function (DateTimeImmutable $datetime, ExecutionContextInterface $context): void {
                        if (!$this->security->isGranted(FutureMonthVoter::ATTRIBUTE, $datetime)) {
                            $context->buildViolation('You cannot manage measurements for future months.')
                                ->addViolation();
                        }
                    }),
                ],
            ])
            ->add('weight', MeasurementType::class, [
                'data' => $measurement?->getWeight(),
                'help' => $this->getHelp($lastMeasurement?->weight, 'kg'),
                'min' => EditMeasurement::WEIGHT_MIN,
                'max' => EditMeasurement::WEIGHT_MAX,
                'attr' => [
                    'autofocus' => true,
                    'step' => 0.1,
                ],
            ])
            ->add('neck', MeasurementType::class, [
                'data' => $measurement?->getNeck(),
                'help' => $this->getHelp($lastMeasurement?->neck),
                'min' => EditMeasurement::NECK_MIN,
                'max' => EditMeasurement::NECK_MAX,
                'required' => false,
            ])
            ->add('chest', MeasurementType::class, [
                'data' => $measurement?->getChest(),
                'help' => $this->getHelp($lastMeasurement?->chest),
                'min' => EditMeasurement::CHEST_MIN,
                'max' => EditMeasurement::CHEST_MAX,
                'required' => false,
            ])
            ->add('shoulders', MeasurementType::class, [
                'data' => $measurement?->getShoulders(),
                'help' => $this->getHelp($lastMeasurement?->shoulders),
                'min' => EditMeasurement::SHOULDERS_MIN,
                'max' => EditMeasurement::SHOULDERS_MAX,
                'required' => false,
            ])
            ->add('waist', MeasurementType::class, [
                'data' => $measurement?->getWaist(),
                'help' => $this->getHelp($lastMeasurement?->waist),
                'min' => EditMeasurement::WAIST_MIN,
                'max' => EditMeasurement::WAIST_MAX,
                'required' => false,
            ])
            ->add('flexedBiceps', MeasurementType::class, [
                'data' => $measurement?->getFlexedBiceps(),
                'help' => $this->getHelp($lastMeasurement?->flexedBiceps),
                'min' => EditMeasurement::BICEPS_MIN,
                'max' => EditMeasurement::BICEPS_MAX,
                'required' => false,
            ])
            ->add('hips', MeasurementType::class, [
                'data' => $measurement?->getHips(),
                'help' => $this->getHelp($lastMeasurement?->hips),
                'min' => EditMeasurement::HIPS_MIN,
                'max' => EditMeasurement::HIPS_MAX,
                'required' => false,
            ])
            ->add('thigh', MeasurementType::class, [
                'data' => $measurement?->getThigh(),
                'help' => $this->getHelp($lastMeasurement?->thigh),
                'min' => EditMeasurement::THIGH_MIN,
                'max' => EditMeasurement::THIGH_MAX,
                'required' => false,
            ])
            ->add('calf', MeasurementType::class, [
                'data' => $measurement?->getCalf(),
                'help' => $this->getHelp($lastMeasurement?->calf),
                'min' => EditMeasurement::CALF_MIN,
                'max' => EditMeasurement::CALF_MAX,
                'required' => false,
            ]);
    }

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

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('lastMeasurement', null);
        $resolver->setDefault('measurement', null);
        $resolver->setAllowedTypes('lastMeasurement', ['null', LastMeasurement::class]);
        $resolver->setAllowedTypes('measurement', ['null', BodyMeasurement::class]);
    }

    private function getHelp(null|float|int $value, string $name = 'cm'): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value) || fmod($value, 1.0) === 0.0) {
            return sprintf('Last: %d %s', $value, $name);
        }

        return sprintf('Last: %.1f %s', $value, $name);
    }
}
