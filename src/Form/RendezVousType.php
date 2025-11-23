<?php

namespace App\Form;

use App\Entity\RendezVous;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateHeureDebut', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 'now'
                ],
                'label' => 'Date et heure de début',
                'html5' => true,
            ])
            ->add('dateHeureFin', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control', 
                    'min' => 'now'
                ],
                'label' => 'Date et heure de fin',
                'html5' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}