<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id')
//            ->add('nom',ChoiceType::class)
            ->add('tipo', 'choice', array(
                'label'=>'Tipo de material:',
                'choices' => array(
                    'file'   => 'Archivo descargable',
                    'link' => 'Link',
                    'test' => 'Test de evaluación',
                    'video' => 'Video',
                ),
                'multiple' => false,
                'required' => true,
            ));
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
