<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo')
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('mail')
            ->add('site', EntityType::class, [
               'class' => Site::class,
               'choice_label' => 'nom',
               'placeholder' => 'Sélectionner un site de rattachement',
           ])


            ->add('plainPassword', PasswordType::class, [

                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
            ])


            ->add('roles', HiddenType::class, [
                'data' => json_encode(['ROLE_USER']),
                'mapped' => false
            ])

            // Checkbox para administrador
            ->add('administrateur', CheckboxType::class, [
                'label' => 'Administrateur',
                'required' => false,  // El checkbox es opcional
                'mapped'   => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {


        $resolver->setDefaults([
            'data_class' => Participant::class,

        ]);
    }
}
