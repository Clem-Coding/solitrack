<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\VolunteerRegistration;
use App\Entity\VolunteerSession;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VolunteerSessionEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('title')
            ->add('description')
            ->add('startDatetime', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('endDatetime', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('requiredVolunteers')

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VolunteerSession::class,
        ]);
    }
}
