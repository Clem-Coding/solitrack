<?php

namespace App\Form;

use App\Entity\CashRegisterSession;
use App\Entity\User;
use App\Entity\Withdrawal;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WithdrawalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', null, [
                'label' => 'Montant du retrait',
                'attr' => [
                    'placeholder' => 'Entrez un montant',
                ]
            ])
            ->add('comment', null, [
                'label' => 'Commentaire',
                'attr' => [
                    'placeholder' => 'Entrez le motif du retrait (facultatif)',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Withdrawal::class,
        ]);
    }
}
