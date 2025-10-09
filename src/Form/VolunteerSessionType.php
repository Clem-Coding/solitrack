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
                'attr' => ['placeholder' => 'Titre de l\'événement'],
                'label_attr' => ['class' => 'sr-only'],
            ])
            ->add('description', null, [
                'label' => 'Description',
                'attr' => ['placeholder' => 'Ajouter une description'],
                'label_attr' => ['class' => 'sr-only'],
            ])
            ->add('location', null, [
                'label' => 'Lieu',
                'attr' => ['placeholder' => 'Ajouter un lieu'],
                'label_attr' => ['class' => 'sr-only'],
            ])
            ->add('from_date', DateType::class, [
                'widget' => 'single_text',
                'mapped' => false,
                'required' => true,
                'label' => 'Date de début',
                'label_attr' => ['class' => 'sr-only'],
            ])
            ->add('from_time', TimeType::class, [
                'widget' => 'single_text',
                'mapped' => false,
                'required' => true,
                'label' => 'Heure de début',
                'label_attr' => ['class' => 'sr-only'],
            ])
            ->add('to_date', DateType::class, [
                'widget' => 'single_text',
                'mapped' => false,
                'required' => true,
                'label' => 'Date de fin',
                'label_attr' => ['class' => 'sr-only'],
            ])
            ->add('to_time', TimeType::class, [
                'widget' => 'single_text',
                'mapped' => false,
                'required' => true,
                'label' => 'Heure de fin',
                'label_attr' => ['class' => 'sr-only'],
            ])
            ->add('required_volunteers', null, [
                'label' => 'Nombre de bénévoles requis',
                'label_attr' => ['class' => 'sr-only'],
                'required' => true,
                'attr' => ['placeholder' => 'Nombre de bénévoles souhaités'],
            ])
            ->add('recurrence', ChoiceType::class, [
                'choices' => [
                    'Jamais' => null,
                    'Tous les jours' => 'daily',
                    'Toutes les semaines' => 'weekly',
                    'Tous les mois' => 'monthly',
                ],
                'choice_value' => fn($choice) => $choice,
                'mapped' => false,
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
