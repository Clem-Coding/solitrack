<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)

            ->add('email', EmailType::class)
            ->add('newPassword', RepeatedType::class, [
                'type' =>  PasswordType::class,
                'mapped' => false,

                'required' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^(?=.*[A-Z])(?=.*[0-9])(?=.*\W)(?!.*\s).{8,4096}$/',
                        'message' => 'Votre mot de passe doit contenir au moins 8 caractères, avec une majuscule, un chiffre et un caractère spécial.',
                    ]),
                    new NotCompromisedPassword([
                        'message' => 'Ce mot de passe a été compromis dans une fuite de données. Veuillez en choisir un autre.',
                    ]),
                ],
                'first_options' => [
                    'hash_property_path' => 'password',
                    'label' => 'label.new_password',
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'mapped' => false,
                'second_options' => [
                    'label' => 'label.new_password_confirm',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
