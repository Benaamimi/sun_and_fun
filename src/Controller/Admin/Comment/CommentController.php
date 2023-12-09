<?php

namespace App\Controller\Admin\Comment;

use App\Entity\Comment;
use App\Form\CommentPublishedType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/comment', name: 'admin_')]
class CommentController extends AbstractController
{
    #[Route('/', name: 'comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findby([], ['createdAt' => 'DESC']);

        return $this->render('admin/comment/index.html.twig', [
            'comments' => $comments
        ]);
    }

    #[Route('/{id}', name: 'comment_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Comment $comment, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CommentPublishedType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $em->flush();


            return $this->redirectToRoute('admin_comment_index');
        }
        
        return $this->render('admin/comment/show.html.twig', [
            'comment' => $comment,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'chambre_delete', methods: ['GET'])]
    public function delete(Comment $comment, EntityManagerInterface $em): Response
    {
        $em->remove($comment);
        $em->flush();

        $this->addFlash(
            'warning',
            'Le commentaires a bien été supprimer!'
        );
               
        return $this->redirectToRoute('admin_comment_index');
    }
}
