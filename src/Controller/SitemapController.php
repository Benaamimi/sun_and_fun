<?php

namespace App\Controller;

use App\Repository\ChambreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'sitemap', defaults:['_format'=>'xml'])]
    public function index(Request $request, ChambreRepository $chambreRepository): Response
    {
        $hostname = $request->getSchemeAndHttpHost();

        $urls = [];

        $urls[] = ['loc' => $this->generateUrl('home')];

        $urls[] = ['loc' => $this->generateUrl('page_environnement')];

        $urls[] = ['loc' => $this->generateUrl('chambre_index')];

        $urls[] = ['loc' => $this->generateUrl('page_contact')];

        foreach($chambreRepository->findAll() as $chambre){
            $urls[] = [
                'loc' => $this->generateUrl('chambre_show', ['id' => $chambre->getId()]),
                'lastmod' => $chambre->getCreatedAt()->format('Y-m-d')
            ];
        }

        $response = new Response(
            $this->renderView('sitemap/index.html.twig', [
                'urls' => $urls,
                'hostname' => $hostname,
            ]),
            200
        );

        $response->headers->set('Content-type', 'text/xml');

        return $response;
    }
}
