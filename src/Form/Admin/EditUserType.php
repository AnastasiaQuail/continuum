<?php

declare(strict_types=1);

namespace Continuum\Form\Admin;

use Continuum\Dto\Request\Admin\User\EditUser;
use Continuum\Entity\User;
use Continuum\Form\Type\AbstractImmutableType;
use Continuum\Security\User\UserStatus;
use Override;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractImmutableType<EditUser>
 */
final class EditUserType extends AbstractImmutableType
{
    #[Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var array{user: User} $options $options */
        $user = $options['user'];

        $builder->setDataMapper($this)
            ->add('status', EnumType::class, [
                'class' => UserStatus::class,
                'data' => $user->status,
                'choices' => [
                    UserStatus::Active,
                    UserStatus::Disabled,
                ],
            ])
            ->add('roles', TextareaType::class, [
                'data' => implode(',', $user->roles),
                'required' => false,
            ]);
    }

    /**
     * @param array{
     *  status: FormInterface<UserStatus>,
     *  roles: FormInterface<null|string>
     * } $forms
     *
     * @phpstan-ignore method.childParameterType (fix of parent stub)
     */
    #[Override]
    protected function mapDataClass(array $forms): EditUser
    {
        $roles = trim($forms['roles']->getData() ?? '');

        return new EditUser(
            $forms['status']->getData(),
            // @phpstan-ignore argument.type (will check by attribute)
            '' === $roles ? [] : explode(',', $roles),
        );
    }

    #[Override]
    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setRequired('user');
        $resolver->setAllowedTypes('user', [User::class]);
    }
}
