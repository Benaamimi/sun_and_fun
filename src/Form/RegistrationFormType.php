<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

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
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir plus que {{ limit }} caractères',
                        'max' => 16,
                        'maxMessage' => 'Le mot de passe ne doit pas dépassé {{ limit }} caractères',
                    ]),
                ],
            ])
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
