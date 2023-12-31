<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
        ->add('roles', ChoiceType::class, [
            'choices' => [
                'Administrateur' => 'ROLE_ADMIN',
                'Utilisateur' => 'ROLE_USER'
            ],
            'expanded' => true,
            'multiple' => true,
        ])
        ->add('email', EmailType::class, [
            'label' => 'Adresse email',
            'attr' => [
                'placeholder' => 'Tapez votre adresse email'
            ],
            'required' => false,
        ])
        ->add('plainPassword', PasswordType::class, [
            'label' => 'Mot de passe',
            'mapped' => false,
            'attr' => [
                'autocomplete' => 'new-password',
                'placeholder' => 'Entrez votre mot de passe'
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'veuillez entrer votre mot de passe',
                ]),
                new Length([
                    'min' => 5,
                    'minMessage' => 'Le mot de passe doit contenir plus que {{ limit }} caractères',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ]),
            ],
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
