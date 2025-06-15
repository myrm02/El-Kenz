<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProductForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('price')
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Pâtisserie' => 'patisserie',
                    'Plats' => 'plats',
                    'Snack' => 'snack',
                    'Boisson' => 'boisson',
                    'Fruit' => 'fruit',
                ],
                'placeholder' => 'Choisissez une catégorie',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
