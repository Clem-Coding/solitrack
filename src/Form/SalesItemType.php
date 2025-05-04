<?php

namespace App\Form;

use App\Entity\Category;
// use App\Entity\Sale;
use App\Entity\SalesItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class SalesItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('weight', null, [
                'label' => 'Poids',
                "row_attr" => [
                    "class" => "hidden",
                    "id" => "weight-input"
                ],

            ])
            ->add('price', null, [
                'label' => 'Prix',
                "row_attr" => [
                    "class" => "hidden",
                    "id" => "price-input"
                ],
            ])
            ->add('quantity', null, [
                'label' => 'Quantité',
                'label_attr' => ['class' => 'sr-only'],
                "row_attr" => [
                    "class" => "hidden",
                    "id" => "quantity-input"
                ],
            ])
            ->add('categoryId', HiddenType::class, [
                'mapped' => false,

            ])
            ->add('sale', HiddenType::class, [
                'mapped' => false,
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SalesItem::class,
            'attr' => [
                'class' => 'sales-form',
            ]
        ]);
    }
}
