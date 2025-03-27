<?php

namespace App\Form;

use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
//        $builder
//            ->add('nom', EntityType::class, [
//                'class' => Ville::class,
//                'choice_label' => 'nom', // Affiche le nom de la ville
//                'attr' => ['class' => 'form-control'],
//                'placeholder' => 'Sélectionnez une ville',
//                'label' => 'Ville',
//            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ville::class,
        ]);
    }
}
