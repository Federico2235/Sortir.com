<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;

class ParticipantProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class)
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('telephone', TextType::class, [
                'required' => false
            ])
            ->add('mail', EmailType::class)
            ->add('password', PasswordType::class, [
                'label' => 'Nouveau mot de passe (laisser vide pour ne pas modifier)',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => '••••••••'
                ],
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                        // La longueur max est gérée par Symfony pour des raisons de sécurité
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('photo', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image(
                        maxSize: '5M',
                        maxSizeMessage: "C'est trop gros !",
                        mimeTypesMessage: "Format non valide !"
                    )
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
            'is_password_required' => false,
        ]);
    }
}