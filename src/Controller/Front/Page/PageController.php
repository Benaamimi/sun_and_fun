<?php

namespace App\Controller\Front\Page;

use App\Entity\Comment;
use App\Entity\Contact;
use App\Form\CommentType;
use App\Form\ContactType;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;

class PageController extends AbstractController
{
    #[Route('/environnement', name: 'page_environnement')]
    public function env(): Response
    {
        return $this->render('front/page/environnement.html.twig');
    }

    #[Route('/contact', name: 'page_contact')]
    public function contact(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {

        $contact = new Contact;

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
        

            $em->persist($contact);
            $em->flush();

             //? envoyer un email de contact
             $email = (new TemplatedEmail())
             ->from($contact->getEmail())
             ->to('sunandfun.chambre@gmail.com')
             ->subject('Email de contact')
             ->htmlTemplate('email/contact_message.html.twig')
             ->context([
                 'contact' => $contact
             ]);
             $mailer->send($email);
             //? Fin de l'envoie

            $this->addFlash("success", "Merci! Votre message est pris en compte.");

            return $this->redirectToRoute('page_contact');
        }


        return $this->render('front/page/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/mention-legal', name: 'page_mention_legal')]
    public function mentionlegal(): Response
    {
        return $this->render('front/page/mentionlegal.html.twig');
    }
}
