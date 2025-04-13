<?php

namespace App\Form;

use App\Entity\Donation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class DonationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->setAttribute('class', 'donation-form')
            ->add('weight', null, [
                'label' => 'Poids'
            ])
            ->add('categoryId', HiddenType::class, [
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Donation::class,
            'attr' => [
                'class' => 'donation-form',
            ],
          
            // 'csrf_protection' => true,                 
            // 'csrf_field_name' => '_token',             
            // 'csrf_token_id'   => 'unique_donation_form'  
        ]);
    }
}
