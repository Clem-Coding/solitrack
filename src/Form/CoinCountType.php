<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;


class CoinCountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $values = [0.01, 0.02, 0.05, 0.1, 0.2, 0.5, 1, 2, 5, 10, 20, 50, 100];


        foreach ([0.01, 0.02, 0.05, 0.1, 0.2, 0.5, 1, 2, 5, 10, 20, 50, 100] as $value) {
            $builder->add('coin_' . str_replace('.', '_', (string) $value), IntegerType::class, [
                'label' => number_format($value, 2, ',', ' ') . ' €',
                'required' => false,
                'empty_data' => '0',
                'attr' => ['step' => 1, 'min' => 0],
            ]);
        }
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        // $resolver->setDefaults([
        //     'csrf_protection' => true,
        //     'csrf_field_name' => '_token',
        //     'csrf_token_id'   => 'cash_closure',
        // ]);
    }
}
