<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\VolunteerRecurrence;
use App\Entity\VolunteerSession;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VolunteerSessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => 'Titre',
            ])
            ->add('description', null, [
                'label' => 'Description',
            ])
            ->add('location', null, [
                'label' => 'Lieu',
            ])
            ->add('from_date', DateType::class, [
                'widget' => 'single_text',
                'mapped' => false,
                'required' => true,
                'label' => 'Date de début',
            ])
            ->add('from_time', TimeType::class, [
                'widget' => 'single_text',
                'mapped' => false,
                'required' => true,
                'label' => 'Heure de début',
            ])
            ->add('to_date', DateType::class, [
                'widget' => 'single_text',
                'mapped' => false,
                'required' => true,
                'label' => 'Date de fin',
            ])
            ->add('to_time', TimeType::class, [
                'widget' => 'single_text',
                'mapped' => false,
                'required' => true,
                'label' => 'Heure de fin',
            ])
            ->add('required_volunteers', null, [
                'label' => 'Nombre de bénévoles requis',
            ])
            ->add('recurrence', ChoiceType::class, [
                'choices' => [
                    'Aucune' => null,
                    'Tous les jours' => 'daily',
                    'Toutes les semaines' => 'weekly',
                    'Tous les mois' => 'monthly',
                ],
                'mapped' => false, // on gère la logique dans le contrôleur
                'required' => false,
                'label' => 'Répéter',
                'placeholder' => 'Choisir une récurrence',
            ])
            ->add('until_date', DateType::class, [
                'widget' => 'single_text',
                'mapped' => false,
                'required' => false,
                'label' => 'Jusqu’à quelle date ?',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VolunteerSession::class,
        ]);
    }
}
