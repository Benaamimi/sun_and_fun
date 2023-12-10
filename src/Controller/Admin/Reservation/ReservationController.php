<?php

namespace App\Controller\Admin\Reservation;

use App\Entity\User;
use App\Entity\Reservation;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use App\Service\MailerService;
use App\Service\ReservationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/reservation', name: 'admin_')]
class ReservationController extends AbstractController
{
    private $reservationService;
    private $mailerService;
    private $em;

    public function __construct(ReservationService $reservationService, 
                                MailerService $mailerService, 
                                EntityManagerInterface $em
                            )
    {  
        $this->reservationService = $reservationService;
        $this->mailerService = $mailerService;
        $this->em = $em;
    }

    #[Route('/', name: 'reservation_index')]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('admin/reservation/index.html.twig', [
            'reservations' => $reservationRepository->findby([], ['createdAt' => 'DESC'])
        ]);
    }

    #[Route('/create', name: 'reservation_create', methods: ['GET', 'POST'])]
    public function create(Reservation $reservation = null, Request $request): Response
    {
        if(!$reservation){
            $reservation = new Reservation;
        }

        $form = $this->createForm(ReservationType::class, $reservation, ['chambre' => true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            //! Reservation de chambre avec injection de dependance
            $this->reservationService->reservationAdmin($reservation);

            $this->em->persist($reservation);
            $this->em->flush();

            //! envoie de mail Utilisateur
            $this->mailerService->sendMailToUser($reservation->getEmail(), $reservation);

             //! envoie de mail Admin
             $this->mailerService->sendMailToAdmin('sunandfun.chambre@gmail.com', $reservation);

            $this->addFlash('success', 'La reservation a bien été confirmer!');
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
    public function edit(Reservation $reservation, Request $request): Response
    {

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->reservationService->reservationAdmin($reservation);

            $this->em->flush();
            
            $this->mailerService->sendMailToUser($reservation->getEmail(), $reservation);

            $this->mailerService->sendMailToAdmin('sunandfun.chambre@gmail.com', $reservation);

            $this->addFlash('success', 'La reservation a bien été modifier, un email a été envoyer.');

            return $this->redirectToRoute('admin_reservation_index');

        }

        return $this->render('admin/reservation/edit.html.twig', [
            'form' => $form,
            'reservation' => $reservation
        ]);
    }

    #[Route('/{id}/delete', name: 'reservation_delete', methods: ['GET'])]
    public function delete(Reservation $reservation): Response
    {
        $this->em->remove($reservation);
        $this->em->flush();

        $this->addFlash('warning', 'La reservation a bien été supprimer!');
               
        return $this->redirectToRoute('admin_reservation_index');
    }

}
