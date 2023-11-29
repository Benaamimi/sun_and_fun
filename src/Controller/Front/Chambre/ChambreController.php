<?php

namespace App\Controller\Front\Chambre;

use App\Entity\Chambre;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ChambreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/chambre/detail/{id}', name: 'chambre_show', methods: ['GET', 'POST'])]
    public function show(Chambre $id, Request $request, ChambreRepository $chambreRepository, EntityManagerInterface $em): Response
    {
        $reservation = new Reservation;
        $chambre = $chambreRepository->find($id);

        $form = $this->createForm(ReservationType::class, $reservation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $checking = $reservation->getCheckingAt();
            $reservation->setChambre($chambre); 
            if ($checking->diff($reservation->getCheckoutAt())->invert == 1) {
                $this->addFlash('danger', 'Une période de temps ne peut pas être négative.');
                if ($reservation->getId())
                    return $this->redirectToRoute('chambre_index', [
                        'id' => $reservation->getId()
                    ]);
                else
                    return $this->redirectToRoute('chambre_index');
            }

            $days = $checking->diff($reservation->getCheckoutAt())->days;
            $prixTotal = ($chambre->getPrixJournalier() * $days) + $chambre->getPrixJournalier();

            $reservation->setPrixTotal($prixTotal);

            $em->persist($reservation);
            $em->flush();
            $this->addFlash('success', 'Votre reservation a bien été valider!');
            return $this->redirectToRoute('home');

        }



        return $this->render('front/chambre/show.html.twig', [
            'formChambre' => $form,
            'chambre' => $chambre
        ]);
    }
}
