<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'app_chambre')]
class ChambreController extends AbstractController
{
    #[Route('/chambre', name: 'app_chambre')]
    public function index(): Response
    {
        return $this->render('chambre/index.html.twig');
    }
}
