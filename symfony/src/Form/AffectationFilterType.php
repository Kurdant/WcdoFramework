<?php

namespace App\Form;

use App\Entity\Fonction;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AffectationFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fonction', EntityType::class, [
                'class' => Fonction::class,
                'choice_label' => 'intitule',
                'required' => false,
                'placeholder' => 'Toutes',
                'label' => 'Poste',
            ])
            ->add('dateDebut', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Date début (après)',
            ])
            ->add('dateFin', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Date fin (avant)',
            ])
            ->add('ville', TextType::class, [
                'required' => false,
                'label' => 'Ville',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
            'required' => false,
        ]);
    }

    public function getBlockPrefix(): string { return ''; }
}
