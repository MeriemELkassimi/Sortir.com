<?php

namespace App\Form;

use App\Entity\Sortie;

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

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
