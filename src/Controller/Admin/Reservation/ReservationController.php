<?php

namespace App\Controller\Admin\Reservation;

use App\Entity\Reservation;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/reservation', name: 'admin_')]
class ReservationController extends AbstractController
{
    #[Route('/', name: 'reservation_index')]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('admin/reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll()
        ]);
    }

    #[Route('/create', name: 'reservation_create', methods: ['GET', 'POST'])]
    public function create(Reservation $reservation = null, Request $request, EntityManagerInterface $em): Response
    {
        if(!$reservation){
            $reservation = new Reservation;
        }

        $form = $this->createForm(ReservationType::class, $reservation, ['chambre' => true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $checking = $reservation->getCheckingAt();

            if ($checking->diff($reservation->getCheckoutAt())->invert == 1) {
                $this->addFlash('danger', 'Une période de temps ne peut pas être négative.');
                if ($reservation->getId())
                    return $this->redirectToRoute('admin_reservation_create', [
                        'id' => $reservation->getId()
                    ]);
                else
                    return $this->redirectToRoute('admin_reservation_create');
            }

            $days = $checking->diff($reservation->getCheckoutAt())->days;
            $prixTotal = ($reservation->getChambre()->getPrixJournalier() * $days) + $reservation->getChambre()->getPrixJournalier();

            $reservation->setPrixTotal($prixTotal);

            $em->persist($reservation);
            $em->flush();
            $this->addFlash('success', 'Votre reservation a bien été valider!');
            return $this->redirectToRoute('admin_reservation_index');

        }

        return $this->render('admin/reservation/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        
        return $this->render('admin/reservation/show.html.twig', [
            'reservation' => $reservation
        ]);
    }

    #[Route('/{id}/edit', name: 'reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Reservation $reservation, Request $request, EntityManagerInterface $em): Response
    {

        $form = $this->createForm(ReservationType::class, $reservation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $checking = $reservation->getCheckingAt();

            if ($checking->diff($reservation->getCheckoutAt())->invert == 1) {
                $this->addFlash('danger', 'Une période de temps ne peut pas être négative.');
                if ($reservation->getId())
                    return $this->redirectToRoute('admin_reservation_create', [
                        'id' => $reservation->getId()
                    ]);
                else
                    return $this->redirectToRoute('admin_reservation_create');
            }

            $days = $checking->diff($reservation->getCheckoutAt())->days;
            $prixTotal = ($reservation->getChambre()->getPrixJournalier() * $days) + $reservation->getChambre()->getPrixJournalier();

            $reservation->setPrixTotal($prixTotal);

        
            $em->flush();

            $this->addFlash('success', 'La reservation a bien été modifier!');

            return $this->redirectToRoute('admin_reservation_index');

        }

        return $this->render('admin/reservation/edit.html.twig', [
            'form' => $form,
            'reservation' => $reservation
        ]);
    }

    #[Route('/{id}/delete', name: 'reservation_delete', methods: ['GET'])]
    public function delete(Reservation $reservation, EntityManagerInterface $em): Response
    {
        $em->remove($reservation);
        $em->flush();

        $this->addFlash(
            'warning',
            'La reservation a bien été supprimer!'
        );
               
        return $this->redirectToRoute('admin_reservation_index');
    }

}
