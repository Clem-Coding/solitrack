<?php

namespace App\Form;

use App\Entity\CashRegisterClosure;
use App\Entity\CashRegisterSession;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CashRegisterClosureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('note')
            ->add('countedBalance', NumberType::class, [
                'mapped' => false,

                'label' => 'Solde compté',
                'attr' => [
                    'id' => 'counted-balance',
                ],
                'attr' => ['placeholder' => '€', 'class' => 'placeholder-right'],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez saisir une valeur pour le solde compté.',
                    ]),
                ],
            ])
            ->add('discrepancy', NumberType::class, [
                'required' => false,
                'label' => 'Écart :',
                'attr' => [
                    'id' => 'discrepancy',
                    'readonly' => true,
                ],
            ])
            ->add('note', null, [
                'attr' => ['placeholder' => 'Justifier l\'écart (facultatif)'],
            ]);

        // Remove leading "+" from discrepancy (added for UX only) to avoid validation error on submit
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            if (isset($data['discrepancy']) && is_string($data['discrepancy'])) {
                $data['discrepancy'] = ltrim($data['discrepancy'], '+');
                $event->setData($data);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CashRegisterClosure::class,
        ]);
    }
}
