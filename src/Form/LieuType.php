<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'attr' => ['class' => 'form-control select-lieu'],
                'placeholder' => 'Sélectionnez un lieu',
                'choices' => []
            ])
            ->add('nom', EntityType::class, [
                'class' => Ville::class,
                'id' => 'villeInput',
                'choice_label' => 'nom',
                'attr' => ['class' => 'form-control select-ville'],
                'placeholder' => 'Sélectionnez une ville',
                'mapped' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
