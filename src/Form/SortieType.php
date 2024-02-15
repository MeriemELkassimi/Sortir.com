<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\CampusRepository;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut',DateTimeType::class, [
                'label' => 'Date et heure de début',
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('duree', null, [
                'label' => 'Durée (min)',
            ])

            ->add('dateLimiteInscription',DateType::class, [
                 'label' => 'Date limite d\'inscription',
                 'html5' => true,
                 'widget' => 'single_text'
            ])
            ->add('nbInscriptionsMax', null, [
                'label' => 'Nombre de places'
            ])
            ->add('infosSortie',TextareaType::class, [
                'label' => 'Description et infos',
                'attr' => ['rows' => 3]
            ])

            ->add('ville', EntityType::class, [
                'label' => 'Ville',
                'class' =>  Ville::class,
                'mapped' => false,
                'choice_label' => 'nom',
                'required' => false,
                'placeholder' => 'Sélectionner une ville',
                'query_builder' => function (VilleRepository  $villeRepository) {
                    return $villeRepository->createQueryBuilder('v')->orderBy('v.nom', 'ASC');
                }
            ])

            ->add('lieu', EntityType::class, [
                'label' => 'Lieu',
                'class' => Lieu::class, 'choice_label' => 'nom',
                'required' => false,
                'placeholder' => 'Sélectionner un lieu',
                'query_builder' => function (LieuRepository  $lieuRepository) {
                    return $lieuRepository->createQueryBuilder('l')->orderBy('l.nom', 'ASC');
                }
            ])
            ->add('annulation',TextareaType::class, [
                'label' => 'Motif :',
                'attr' => ['rows' => 3]
            ])


    ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
