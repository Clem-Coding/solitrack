<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\sale;
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
            ->add('weigth', null, [
                'label' => 'Poids'
            ])
            ->add('price', null, [
                'label' => 'Prix'
            ])
            ->add('quantity', null, [
                'label' => 'Quantité'
            ])
            ->add('quantity', null, ['attr' => ['id' => 'quantity']])
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
        ]);
    }
}
