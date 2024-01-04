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
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
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
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'attr' => [
                    'placeholder' => 'Tapez votre adresse email'
                ],
                'required' => false,
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type'=> PasswordType::class,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmer le mot de passe'],
                'invalid_message' => 'les mots de passes ne correspondent pas',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrez votre mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe de doit pas avoir moins de {{ limit }} charactères',
                        // max length allowed by Symfony for security reasons
                        'max' => 20,
                        'maxMessage' => 'Le mot de passe de doit pas avoir plus de {{ limit }} charactères',
                    ]),
                ],
                'required' => false,
            ])
            // ->add('plainPassword', PasswordType::class, [
            //     'label' => 'Mot de passe',
            //     'required' => false,
            //     'mapped' => false,
            //     'attr' => [
            //         'autocomplete' => 'new-password',
            //         'placeholder' => 'Entrez votre mot de passe'
            //     ],
            //     'constraints' => [
            //         new NotBlank([
            //             'message' => 'veuillez entrer votre mot de passe',
            //         ]),
            //         new Length([
            //             'min' => 6,
            //             'minMessage' => 'Le mot de passe doit contenir plus que {{ limit }} caractères',
            //             'max' => 16,
            //             'maxMessage' => 'Le mot de passe ne doit pas dépassé {{ limit }} caractères',
            //         ]),
            //     ],
            // ])
        //     ->add('roles', ChoiceType::class, [
        //         'choices' => [
        //             'Admin' => 'ROLE_ADMIN',
        //             'Membre' => 'ROLE_USER'
        //         ],
        //         'expanded' => true,
        //         'multiple' => true,
        //     ])
         ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
