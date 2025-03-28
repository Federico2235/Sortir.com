<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\LessThan;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Nom de la sortie'
                ],
                'label_attr' => ['class' => 'label']
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'input',
                    'min' => (new \DateTime())->format('Y-m-d\TH:i')
                ],
                'label_attr' => ['class' => 'label']
            ])
            ->add('duree', IntegerType::class, [
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Durée en minutes'
                ],
                'label_attr' => ['class' => 'label']
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Nombre maximum de participants'
                ],
                'label_attr' => ['class' => 'label']
            ])
            ->add('infosSortie', TextareaType::class, [
                'attr' => [
                    'class' => 'textarea',
                    'placeholder' => 'Description détaillée',
                    'rows' => 4
                ],
                'label_attr' => ['class' => 'label']
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $sortie = $event->getData();
            $form = $event->getForm();

            $form->add('dateLimiteInscription', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'input',
                    'min' => (new \DateTime())->format('Y-m-d\TH:i')
                ],
                'label_attr' => ['class' => 'label'],
                'constraints' => [
                    new LessThan([
                        'propertyPath' => 'parent.all[dateHeureDebut].data',
                        'message' => 'La date limite doit être antérieure à la date de début'
                    ])
                ]
            ]);
        });

        // Ajout de la validation dynamique lors de la modification
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            if (isset($data['dateHeureDebut'])) {
                $form->add('dateLimiteInscription', DateTimeType::class, [
                    'widget' => 'single_text',
                    'attr' => [
                        'max' => $data['dateHeureDebut']
                    ]
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'attr' => ['class' => 'form-container box']
        ]);
    }
}