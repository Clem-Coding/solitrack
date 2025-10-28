<?php

namespace App\Form;

use App\Entity\OutgoingWeighing;
use App\Enum\OutgoingWeighingType as EnumOutgoingWeighingType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class OutgoingWeighingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', EnumType::class, [
                'class' => EnumOutgoingWeighingType::class,
                'label' => 'Type de mouvement',
                'label_attr' => ['class' => 'sr-only'],
                'choice_label' => fn(EnumOutgoingWeighingType $choice) => $choice->getLabel(),
                'expanded' => true,
                'attr' => ['class' => 'radio-inline'],
            ])
            ->add('weight', null, [
                'label' => 'Poids',
                'attr' => ['placeholder' => 'kg', 'class' => 'placeholder-right'],

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OutgoingWeighing::class,
        ]);
    }
}
