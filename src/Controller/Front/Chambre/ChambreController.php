<?php

namespace App\Controller\Front\Chambre;

use App\Entity\Chambre;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ChambreRepository;
use App\Service\MailerService;
use App\Service\ReservationService;
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
        $chambreDisponible = $chambreRepository->findChambresNonReservees();

        return $this->render('front/chambre/index.html.twig', [
            'chambres' => $chambreDisponible
        ]);
    }

    #[Route('/chambre/detail/{id}', name: 'chambre_show', methods: ['GET', 'POST'])]
    public function show(Chambre $id, Request $request, ChambreRepository $chambreRepository, EntityManagerInterface $em, MailerService $mailerService, ReservationService $reservationService): Response
    {
        $reservation = new Reservation;
        $chambre = $chambreRepository->find($id);
        
        $form = $this->createForm(ReservationType::class, $reservation, ['chambre' => false]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
           
            //! Reservation de chambre avec injection de dependance
            $reservationService->reservationUser($reservation, $chambre);

            $em->persist($reservation);
            $em->flush();

            //! envoie de mail Utilisateur
            $mailerService->sendMailToUser($reservation->getEmail(), $reservation);

            //! envoie de mail Admin
            $mailerService->sendMailToAdmin('sunandfun.chambre@gmail.com', $reservation);

            $this->addFlash('success', 'Merci pour votre reservation! Vous avez reçu un email de confirmation.');
            return $this->redirectToRoute('chambre_index');

        }

        return $this->render('front/chambre/show.html.twig', [
            'formChambre' => $form,
            'chambre' => $chambre
        ]);
    }
}
