<?php

namespace App\Controller\Front\Chambre;

use App\Entity\Chambre;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ChambreRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


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
    public function show(Chambre $id, Request $request, ChambreRepository $chambreRepository, EntityManagerInterface $em, MailerService $mailerService): Response
    {
        $reservation = new Reservation;
        $chambre = $chambreRepository->find($id);
        
        $form = $this->createForm(ReservationType::class, $reservation, ['chambre' => false]);

        
        
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
