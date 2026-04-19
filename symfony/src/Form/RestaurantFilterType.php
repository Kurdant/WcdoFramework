<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RestaurantFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['required' => false, 'label' => 'Nom'])
            ->add('codePostal', TextType::class, ['required' => false, 'label' => 'Code postal'])
            ->add('ville', TextType::class, ['required' => false, 'label' => 'Ville']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
            'required' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
