<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_inscription')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        //! création du formulaire a partir de la class User avec le RegistrationFormType::class

        $form->handleRequest($request);
        //! inspercte la requette si le formulaire a été soumis il prend les infos mis dans le formulaire


        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            //? perparer

            $entityManager->flush();
            //? mettre en base de données


            // do anything else you need here, like send an email

            $this->addFlash("success", "Vous êtes bien enregistré, Vous pouvez vous indentifier");

            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(), 
            //! la fonction createView pour l'afficher avec twig
        ]);
    }
}
