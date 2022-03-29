<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreSortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('site',EntityType::class,[
                'class'=>Site::class,
                'choice_label'=>"nom"
            ])
            ->add('nom',TextType::class,["label"=>"Description"])
            ->add('dateHeureDebut',DateType::class,["label"=>"Entre"])
            ->add('organisateur')
            ->add('etat',EntityType::class,[
                'class'=>Etat::class,
                'choice_label'=>"libelle"
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
