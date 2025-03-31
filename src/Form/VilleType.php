<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Repository\LieuRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

class VilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = new DynamicFormBuilder($builder);

        // Add 'ville' field
        $builder
            ->add('nom', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez une ville',
                'attr' => ['class' => 'form-control'],
                'mapped' => false,
            ])
            ->add('nom',EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez un lieu',
                'attr' => ['class' => 'form-control'],
                'mapped' => false]);

        // Add dependent 'lieu' field
        $builder->addDependent('lieu', 'ville', function (DependentField $scdBuilder, Ville $ville = null) {
            if ($ville === null) {
                return;
            }

            $scdBuilder->add(EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez un lieu',
                'attr' => ['class' => 'form-control'],
                'mapped' => false,
                'query_builder' => function (LieuRepository $er) use ($ville) {
                    return $er->createQueryBuilder('l')
                        ->where('l.ville = :ville')
                        ->setParameter('ville', $ville);
                },
            ]);
        });
    }

    public
    function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ville::class,
        ]);
    }
}