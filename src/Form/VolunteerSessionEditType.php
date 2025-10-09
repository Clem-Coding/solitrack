<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\VolunteerRegistration;
use App\Entity\VolunteerSession;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VolunteerSessionEditType extends VolunteerSessionType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        parent::buildForm($builder, $options);
        $builder->remove('recurrence');
        $builder->remove('until_date');
        $builder->add('add_volunteer', EntityType::class, [
            'class' => User::class,
            'choice_label' => 'firstName',
            'required' => false,
            'label' => 'Ajouter un bénévole',
            'label_attr' => ['class' => 'sr-only'],
            'mapped' => false,
            'query_builder' => function (\Doctrine\ORM\EntityRepository $er) {
            return $er->createQueryBuilder('u')
                ->orderBy('u.firstName', 'ASC');
            },
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VolunteerSession::class,
        ]);
    }
}
