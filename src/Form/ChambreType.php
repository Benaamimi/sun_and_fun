<?php

namespace App\Form;

use App\Entity\Chambre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChambreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Nom de la chambre',
                'attr' => [
                    'placeholder' => 'tapez le nom de la chambre'
                ],
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Description de la chambre'
                ],
                'required' => false,
                'required' => false,
            ])
            ->add('prixJournalier', MoneyType::class, [
                'label' => 'Prix par nuit',
                'attr' => [
                    'placeholder' => 'Tapez le prix du produit en euro',
                ],
                // 'divisor' => 100,
                'required' => false,
            ])
            ->add('image', FileType::class, [
                'label' => 'Ajouter une image principale',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ est obligatoire '
                    ]),
                    new File([
                        'maxSize' => '3m',
                        'maxSizeMessage' => 'Le fichier ne doit pas dépasser 3 Mo en poids.',
                    ])
                ],
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
