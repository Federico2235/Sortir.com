<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Form\EventListener\AddLieuFieldSubscriber;
use App\Repository\LieuRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

class VilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez une ville',
                'attr' => ['class' => 'form-control'],
                'mapped' => false,
                'method' => 'GET',
            ]);

        $lieuxAdjundtion = function (FormInterface $form, ?Ville $ville = null): void {
            $lieux = null === $ville ? [] : $ville->getLieux();

            $form->add('lieu', EntityType::class, ['class' => Lieu::class,
                    'choice_label' => 'nom',
                    'placeholder' => 'Sélectionnez un lieu',
                    'attr' => ['class' => 'form-control'],
                    'mapped' => false,
                    'query_builder' =>
                        function (LieuRepository $lieuRepository) use ($lieux) {
                            return $lieuRepository->createQueryBuilder('l')
                                ->where('l.ville = :ville')
                                ->setParameter('ville', $lieux);
                        }
                ]
            );
        };

        $builder
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) use ($lieuxAdjundtion) {
                    $ville = $event->getData();
                    $lieuxAdjundtion($event->getForm(), $ville);
                }
            );

        $builder
            ->get('ville')->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) use ($lieuxAdjundtion): void {
                    $ville = $event->getData();
                    $lieuxAdjundtion($event->getForm()->getParent(), $ville);
                }
            );

        $builder->setAction($options['action']);
    }

    public
    function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ville::class,
        ]);
    }
}