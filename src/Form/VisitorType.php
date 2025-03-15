<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Visitor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VisitorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('count', NumberType::class, [
                'html5' => true,
                'attr' => [
                    'min' => 0,
                    'step' => 1 // Pour éviter les décimaux
                ],
            ])
            ->add('date', null, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable'
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Visitor::class,
        ]);
    }
}
