<?php

namespace App\Form;

use App\Entity\Sale;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            // ->add('totalPrice')
            // ->add('cashAmount', NumberType::class, [
            //     'label' => 'Espèces',
            //     'attr' => ['class' => 'cash-input']
            // ])
            // ->add('cardAmount', NumberType::class, [
            //     'label' => 'Carte Bleue',
            //     'attr' => ['class' => 'card-input']
            // ])
            ->add('zipcodeCustomer')
            // ->add('keepChange')
            // ->add('pwywAmount')

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sale::class,
        ]);
    }
}
