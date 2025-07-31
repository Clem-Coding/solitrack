<?php

namespace App\Form;

use App\Entity\CashRegisterSession;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CashFloatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('cashFloat', null, [
                'label' => 'Montant du fond de caisse',
                'attr' => ['placeholder' => 'Saisissez le montant'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CashRegisterSession::class,
        ]);
    }
}
