<?php

namespace App\Controller\Front\Page;

use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/environnement', name: 'page_environnement')]
    public function env(): Response
    {
        return $this->render('front/page/environnement.html.twig');
    }

    #[Route('/contact', name: 'page_contact')]
    public function contact(Request $request, EntityManagerInterface $em): Response
    {

        $comment = new Comment;

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
        

            $em->persist($comment);
            $em->flush();


            $this->addFlash("success", "Merci! Votre avis est pris en compte.");

            return $this->redirectToRoute('page_contact');
        }


        return $this->render('front/page/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
