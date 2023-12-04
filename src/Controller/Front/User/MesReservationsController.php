<?php

namespace App\Controller\Front\User;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MesReservationsController extends AbstractController
{
    #[Route('/mes-reservations', name: 'mes_reservations')]
    public function index(): Response
    {

        return $this->render('front/mes_reservations/index.html.twig', [
            
        ]);
    }
}
