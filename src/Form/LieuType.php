<?php

namespace App\Form;

use App\Entity\Lieu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
//        $villeId = $options['ville_id'];
//
        $builder
            ->add('nom', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Sélectionnez un lieu',
//                'choices' => function (Options $options) use ($villeId) {
//                        $lieuRepository = $options['em']->getRepository(Lieu::class);
//                        return $lieuRepository->findBy(['ville' => $villeId]);
//                    },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
