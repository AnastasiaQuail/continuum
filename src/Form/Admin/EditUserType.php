<?php

declare(strict_types=1);

namespace Continuum\Form\Admin;

use Continuum\Dto\Request\Admin\User\EditUser;
use Continuum\Entity\User;
use Continuum\Form\Type\AbstractImmutableType;
use Continuum\Security\User\UserStatus;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EditUserType extends AbstractImmutableType
{
    /**
     * @param array{user: User} $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];

        $builder->setDataMapper($this)
            ->add('status', EnumType::class, [
                'class' => UserStatus::class,
                'data' => $user->getStatus(),
                'choices' => [
                    UserStatus::Active,
                    UserStatus::Disabled,
                ],
            ])
            ->add('roles', TextareaType::class, [
                'data' => implode(',', $user->getRoles()),
                'required' => false,
            ]);
    }

    protected function mapDataClass(array $forms): EditUser
    {
        $roles = trim($forms['roles']->getData() ?? '');

        return new EditUser(
            $forms['status']->getData(),
            '' === $roles ? [] : explode(',', $roles),
        );
    }

    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setRequired('user');
        $resolver->setAllowedTypes('user', [User::class]);
    }
}
