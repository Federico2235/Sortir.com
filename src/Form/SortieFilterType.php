<?php

namespace App\Form;

use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Sélection du site souhaité
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
                'label' => 'Choisissez un site',
                'required' => false,
                'placeholder' => '- Aucune préférence -',
                'data' => $options['default_site'] ?? null,
                'attr' => ['class' => 'form-control']
            ])
//            // Recherche de concordence avec la saisie
            ->
            add('nom', TextType::class, [
                'required' => false,
                'label' => 'Le nom de la sortie contient',
                'attr' => [
                    'placeholder' => 'Rechercher par nom',
                    'class' => 'form-control'],
            ])
            // INTERVAL
            //// Sélection du Début de l'interval
            ->add('dateDebut', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Entre',
                'attr' => ['placeholder' => 'Date de début'],
            ])
            //// Sélection de la Fin de l'interval
            ->add('dateFin', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'et',
                'attr' => ['placeholder' => 'Date de fin'],
            ])
            // CHECKLIST
            //// Checkpoint Organisateur
            ->add('organisateur', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties dont je suis l\'organisateur/trice',
            ])
            // Checkpoint Participant
            ->add('inscrit', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties auxquelles je suis inscrit/e',
            ])
            // Checkpoint Non-Participant
            ->add('nonInscrit', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
            ])
            // Checkpoint Anciennes sortie
            ->add('terminee', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties terminées',
            ])
            // Bouton de Validation des filtres
//            ->add('submit', SubmitType::class, [
//                'label' => 'Rechercher',
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null, // Pas de classe d'entité spécifique pour ce formulaire
        ]);
    }
}
