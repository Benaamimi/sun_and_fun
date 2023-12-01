<?php

namespace App\Controller\Admin\Chambre;

use App\Entity\Chambre;
use App\Form\ChambreType;
use App\Repository\ChambreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/chambre', name: 'admin_')]
class ChambreController extends AbstractController
{

    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    #[Route('/', name: 'chambre_index', methods: ['GET'])]
    public function index(ChambreRepository $chambreRepository): Response
    {
        return $this->render('admin/chambre/index.html.twig', [
            'chambres' => $chambreRepository->findAll()
        ]);
    }

    #[Route('/create', name: 'chambre_create',  methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $chambre = new Chambre;

        $form = $this->createForm(ChambreType::class, $chambre);
        //? création du formulaire a partir de la class Chambre avec le ChambreType::class

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            // $chambre = $form->getData();

            $imageFile = $form->get('image')->getData();

            
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $this->slugger->slug($originalFilename);

                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('img_upload'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }
                $chambre->setImage($newFilename);

            }

            $em->persist($chambre);
            $em->flush();

            $this->addFlash("success", "la Chambre à bien été ajouter !");

            return $this->redirectToRoute('admin_chambre_index');
        }

        return $this->render('admin/chambre/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'chambre_show', methods: ['GET'])]
    public function show(Chambre $chambre): Response
    {
        
        return $this->render('admin/chambre/show.html.twig', [
            'chambre' => $chambre
        ]);
    }

    #[Route('/{id}/edit', name: 'chambre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chambre $chambre, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ChambreType::class, $chambre);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $imageFile = $form->get('image')->getData();

            
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $this->slugger->slug($originalFilename);

                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('img_upload'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }
                $chambre->setImage($newFilename);

            }

            $em->flush();


            $this->addFlash(
                'success',
                'La chambre a bien été modifier!'
            );

            return $this->redirectToRoute('admin_chambre_index');
        }

        return $this->render('admin/chambre/edit.html.twig', [
            'chambre' => $chambre,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'chambre_delete', methods: ['GET'])]
    public function delete(Chambre $chambre, EntityManagerInterface $em): Response
    {
        $em->remove($chambre); //* Suppression de la chambre
        $em->flush();

        $this->addFlash(
            'warning',
            'La chambre a bien été supprimer!'
        );
               
        return $this->redirectToRoute('admin_chambre_index');
    }
}