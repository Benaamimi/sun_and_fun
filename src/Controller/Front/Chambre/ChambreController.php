<?php

namespace App\Controller\Front\Chambre;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ChambreController extends AbstractController
{
    #[Route('/chambre', name: 'chambre_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('chambre/index.html.twig');
    }
}
