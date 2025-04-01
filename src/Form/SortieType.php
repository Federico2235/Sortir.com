<?php

namespace App\Form;

use App\Entity\Sortie;
use phpDocumentor\Reflection\PseudoTypes\IntegerValue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;


class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [  // <-- Spécifiez explicitement TextType
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom est obligatoire',
                        'groups' => ['create', 'update']  // <-- Définissez des groupes de validation
                    ]),
                    new Length([
                        'min' => 5,
                        'max' => 100,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères',
                        'groups' => ['create', 'update']
                    ])
                ],
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Nom de la sortie',
                    'minlength' => 5,  // <-- Validation HTML5 côté client
                    'maxlength' => 100,
                    'required' => true
                ],
                'label_attr' => ['class' => 'label'],
                'required' => true  // <-- Important pour la validation HTML5
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'input',
                    'min' => (new \DateTime())->format('Y-m-d\TH:i')
                ],
                'label_attr' => ['class' => 'label'],
                'constraints' => [  // <-- Les contraintes doivent être dans ce tableau
                    new Assert\NotBlank(['message' => 'La date de début est obligatoire']),
                    new Assert\GreaterThan([
                        'value' => 'today',
                        'message' => 'La date de début doit être dans le futur'
                    ])
                ]
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
                    'placeholder' => 'Nombre maximum de participants',
                    'min' => 1,       // Validation HTML5
                    'max' => 1000      // Validation HTML5
                ],
                'label_attr' => ['class' => 'label'],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nombre de participants est obligatoire',
                        'groups' => ['create', 'update']
                    ]),
                    new Assert\Range([
                        'min' => 1,
                        'max' => 1000,
                        'notInRangeMessage' => 'Le nombre de participants doit être entre {{ min }} et {{ max }} personnes',
                        'groups' => ['create', 'update']
                    ])
                ]
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