<?php

namespace App\Controller\Front\Page;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function contact(): Response
    {
        return $this->render('front/page/contact.html.twig');
    }
}
