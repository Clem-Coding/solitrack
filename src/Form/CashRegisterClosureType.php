<?php

namespace App\Form;

use App\Entity\CashRegisterClosure;
use App\Entity\CashRegisterSession;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CashRegisterClosureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('note')
            ->add('countedBalance', NumberType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Solde compté',
                'attr' => [
                    'id' => 'counted-balance',
                    'readonly' => true,
                ],
            ])
            ->add('discrepancy', NumberType::class, [
                'required' => false,
                'label' => 'Écart',
                'attr' => [
                    'id' => 'discrepancy',
                    'readonly' => true,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CashRegisterClosure::class,
        ]);
    }
}
