<?php

namespace App\Controller\Front\Chambre;

use App\Entity\Chambre;
use App\Repository\ChambreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ChambreController extends AbstractController
{
    #[Route('/chambre', name: 'chambre_index', methods: ['GET'])]
    public function index(ChambreRepository $chambreRepository): Response
    {
        return $this->render('front/chambre/index.html.twig', [
            'chambres' => $chambreRepository->findAll()
        ]);
    }

    #[Route('/chambre/{id}', name: 'chambre_show', methods: ['GET'])]
    public function show(Chambre $chambre): Response
    {
        return $this->render('front/chambre/show.html.twig', [
            'chambre' => $chambre
        ]);
    }
}
