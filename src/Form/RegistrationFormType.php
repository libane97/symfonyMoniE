<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('password', PasswordType::class)
            ->add('confirm_password',  PasswordType::class)
            ->add('roles', CollectionType::class, [
            'entry_type'   => ChoiceType::class,
            'entry_options'  => [
                'label' => false,
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'Super' => 'ROLE_SUPER_ADMIN',
                    'Client' => 'ROLE_CLIENT',
                ],
            ],
    ]) 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
