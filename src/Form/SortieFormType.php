<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut', DateTimeType::class)
            ->add('duree')
            ->add('dateLimiteInscription', DateTimeType::class)
            ->add('nbInscriptionMax')
            ->add('infosSortie')
            ->add('SortieLieu', EntityType::class,[
                'class'=>Lieu::class,
                'choice_label'=>function(Lieu $lieu){
                return $lieu->getNom();
                },
                'mapped'=>false,
                'multiple'=>false,
                'expanded'=>true
            ])
            ->add('creerSortie',SubmitType::class, [
            ])
            ->add('reset',ResetType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
