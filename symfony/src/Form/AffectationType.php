<?php

namespace App\Form;

use App\Entity\Affectation;
use App\Entity\Collaborateur;
use App\Entity\Fonction;
use App\Entity\Restaurant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AffectationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['lock_collaborateur']) {
            $builder->add('collaborateur', EntityType::class, [
                'class' => Collaborateur::class,
                'choice_label' => fn (Collaborateur $c) => sprintf('%s %s', $c->getPrenom(), $c->getNom()),
                'label' => 'Collaborateur',
                'placeholder' => '— Sélectionner —',
            ]);
        }

        if (!$options['lock_restaurant']) {
            $builder->add('restaurant', EntityType::class, [
                'class' => Restaurant::class,
                'choice_label' => fn (Restaurant $r) => sprintf('%s (%s)', $r->getNom(), $r->getVille()),
                'label' => 'Restaurant',
                'placeholder' => '— Sélectionner —',
            ]);
        }

        $builder
            ->add('fonction', EntityType::class, [
                'class' => Fonction::class,
                'choice_label' => 'intitule',
                'label' => 'Fonction',
                'placeholder' => '— Sélectionner —',
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'required' => false,
                'help' => 'Laisser vide pour une affectation en cours.',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Affectation::class,
            'lock_collaborateur' => false,
            'lock_restaurant' => false,
        ]);
        $resolver->setAllowedTypes('lock_collaborateur', 'bool');
        $resolver->setAllowedTypes('lock_restaurant', 'bool');
    }
}
