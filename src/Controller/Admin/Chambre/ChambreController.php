<?php

namespace App\Controller\Admin\Chambre;

use App\Entity\Chambre;
use App\Form\ChambreDisponibleType;
use App\Form\ChambreType;
use App\Repository\ChambreRepository;
use App\Service\ImageUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/chambre', name: 'admin_')]
class ChambreController extends AbstractController
{

    private $imageUpload;

    public function __construct(ImageUploadService $imageUpload)
    {
        $this->imageUpload = $imageUpload;
    }

    #[Route('/', name: 'chambre_index', methods: ['GET'])]
    public function index(ChambreRepository $chambreRepository): Response
    {
        return $this->render('admin/chambre/index.html.twig', [
            'chambres' => $chambreRepository->findBy([], ['titre'=>'ASC'])
        ]);
    }

    #[Route('/create', name: 'chambre_create',  methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $chambre = new Chambre;

        //! création du formulaire a partir de la class Chambre avec le ChambreType::class
        $form = $this->createForm(ChambreType::class, $chambre);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            //! image upload avec injection de dependance
            $this->imageUpload->ImageUpload($form, $chambre);

            //! mettre en base de donnée
            $em->persist($chambre);
            $em->flush();


            $this->addFlash("success", "la Chambre à bien été ajouter !");

            return $this->redirectToRoute('admin_chambre_index');
        }

        return $this->render('admin/chambre/create.html.twig', [
            //! formulaire dans vue (twig)
            'form' => $form->createView(), 
        ]);
    }

    #[Route('/{id}', name: 'chambre_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Chambre $chambre, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ChambreDisponibleType::class, $chambre);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $em->flush();


            return $this->redirectToRoute('admin_chambre_index');
        }
        
        return $this->render('admin/chambre/show.html.twig', [
            'chambre' => $chambre,
            'form' => $form
        ]);
    }

    #[Route('/{id}/edit', name: 'chambre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chambre $chambre, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ChambreType::class, $chambre);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){


            $this->imageUpload->ImageUpload($form, $chambre);

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
        $em->remove($chambre); //! Suppression de la chambre
        $em->flush();

        $this->addFlash(
            'warning',
            'La chambre a bien été supprimer!'
        );
               
        return $this->redirectToRoute('admin_chambre_index');
    }
}