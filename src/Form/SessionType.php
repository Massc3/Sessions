<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Formation;
use App\Form\ProgrammeType;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            // ->add('formation', EntityType::class, [
            //     'class' => Formation::class,
            //     'attr' => [
            //         'class' => 'form-control'
            //     ]
            // ])
            ->add('programmes', CollectionType::class, [
                // La collection attend l'element qu'elle entrera dans le form mais que c'est pas automatiquement un autre formulaire
                'entry_type' => ProgrammeType::class,
                'prototype' => true,
                // on va autoriser l'ajout d'un nouvel element dans l'entité session, qui seront persiste grace a cascade_persist sur l'element Programme et ca va activer un data prototype qui sera un attribut html qu'on pourra manipuler en javascript
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false, //il est obligatoire car Session n'a pas de setProgramme mais c'est Programme qui contient setSession
                // c'est Programme qui propriétaire de la relation, pour eviter un mapping false on est obliger de rajouter en byReference
            ])

            ->add('ajouter', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-sucess'
                ]
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
