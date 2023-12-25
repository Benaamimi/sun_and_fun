<?php

namespace App\Form;

use App\Entity\Chambre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ChambreDisponibleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        // ->add('isDisponible', ChoiceType::class, [
        //     'choices' => [
        //         'Disponible' => true,  
        //         'En attente' => false
        //     ],
        //     'label' => 'Disponibilité',
        //     'expanded' => true,
        //     'multiple' => false,
        //     'data' => true
        // ])
        // ;
        ->add('isDisponible', CheckboxType::class, [
            'label' => 'Disponible',
            'required' => false
        ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chambre::class,
        ]);
    }
}
