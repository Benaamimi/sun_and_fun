<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('roles', ChoiceType::class, [
            'choices' => [
                'Admin' => 'ROLE_ADMIN',
            ],
            'expanded' => true,
            'multiple' => true,
        ])
        ->add('civilite', ChoiceType::class, [
            'label' => 'Civilité',
            'choices'  => [
                'M.' => 'M.',
                'Mme.' => 'Mme.',
            ],
        ])
        ->add('prenom', TextType::class, [
            'label' => 'Prénom',
            'attr' => [
                'placeholder' => 'Tapez votre prénom'
            ],
            'required' => false,
        ])
        ->add('nom', TextType::class, [
            'label' => 'Nom',
            'attr' => [
                'placeholder' => 'Tapez votre nom'
            ],
            'required' => false,
        ])
        ->add('email', EmailType::class, [
            'label' => 'Adresse email',
            'attr' => [
                'placeholder' => 'Tapez votre adresse email'
            ],
            'required' => false,
        ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
