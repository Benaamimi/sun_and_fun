<?php

namespace App\Controller\Admin\Chambre;

use App\Entity\Chambre;
use App\Form\ChambreType;
use App\Repository\ChambreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/chambre', name: 'admin_')]
class ChambreController extends AbstractController
{
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

            $em->persist($chambre);
            $em->flush();

            $this->addFlash("success", "la Chambre à bien été ajouter !");

            return $this->redirectToRoute('admin_chambre_index');
        }

        return $this->render('admin/chambre/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}