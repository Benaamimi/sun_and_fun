<?php

namespace App\Controller\Front\Home;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, EntityManagerInterface $em, CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findBy(['isPublished' => true], ['createdAt' => 'DESC'], 6);

        $comment = new Comment;

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
        

            $em->persist($comment);
            $em->flush();


            $this->addFlash("success", "Merci! Votre avis est pris en compte.");

            return $this->redirectToRoute('home');
        }

        return $this->render('front/home/index.html.twig', [
            'comments' => $comments,
            'form' => $form->createView()
        ]);
    }
}
