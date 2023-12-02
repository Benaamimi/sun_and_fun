<?php

namespace App\Form;

use App\Entity\Chambre;
use App\Entity\Reservation;
use App\Repository\ChambreRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['chambre']) {
            $builder->add('chambre',  EntityType::class, [
                'class' => Chambre::class,
                'choice_label' => 'titre',
                'query_builder' => function (ChambreRepository $er) {
                    return $er->createQueryBuilder('c')
                            ->orderBy('c.titre', 'ASC'); // Remplacez 'nom' par le champ que vous souhaitez utiliser pour le tri
                },
            ]);
        }
        $builder
            ->add('checkingAt', DateTimeType::class, [
                'label' => 'Date d\'arrivée',
                'widget' => 'single_text',
                 'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d H:i'), // permet d'empecher de choisir une date ultérieure à celle d'aujourd'hui (voir doc datetime)
                ]
            ])
            ->add('checkoutAt', DateTimeType::class, [
                'label' => 'Date de sortie',
                'widget' => 'single_text',
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d H:i'), // permet d'empecher de choisir une date ultérieure à celle d'aujourd'hui (voir doc datetime)
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => false
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('personneNumber', NumberType::class,[
                'label' => 'Nombre de personne (Moins de 3 personne par chambre)'
            ])
            ->add('phone', NumberType::class, [
                'label' => 'Numero de téléphone',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'chambre' => false
        ]);
    }
}
