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

            ->add('zipcodeCustomer', null, [
                'label' => "Code postal",
                'attr' => [
                    'pattern' => '\d{5}',
                    'oninvalid' => "this.setCustomValidity('Le code postal doit contenir exactement 5 chiffres.')",
                    'oninput' => "this.setCustomValidity('')"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sale::class,
        ]);
    }
}
