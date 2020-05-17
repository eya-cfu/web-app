<?php

namespace App\Form;

use App\Entity\Boulangerie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BoulangerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomBL')
            ->add('adresse')
            ->add('telephone')
            ->add('nbOperateurs')
            ->add('matricule')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Boulangerie::class,
        ]);
    }
}
