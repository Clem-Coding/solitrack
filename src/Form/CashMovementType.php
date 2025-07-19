<?php

namespace App\Form;

use App\Entity\CashRegisterSession;
use App\Entity\User;
use App\Entity\CashMovement;
use App\Enum\CashMovementAction;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class CashMovementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', EnumType::class, [
                'class' => CashMovementAction::class,
                'label' => 'Type de mouvement',
                'label_attr' => ['class' => 'sr-only'],
                'choice_label' => fn(CashMovementAction $choice) => $choice->getLabel(),
                'expanded' => true,
                'attr' => ['class' => 'radio-inline'],
            ])
            ->add('amount', null, [
                'label' => 'Montant',
                'attr' => [
                    'placeholder' => 'Entrez un montant',
                ]
            ])
            ->add('comment', null, [
                'label' => 'Commentaire',
                'attr' => [
                    'placeholder' => 'Entrez le motif de l\'opération (facultatif)',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CashMovement::class,
        ]);
    }
}
