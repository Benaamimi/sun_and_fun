<?php

namespace App\Controller\Front\Chambre;

use Stripe\Stripe;
use App\Entity\Chambre;
use App\Entity\Reservation;
use Stripe\Checkout\Session;
use App\Form\ReservationType;
use App\Service\MailerService;
use App\Service\ReservationService;
use App\Repository\ChambreRepository;
use App\Service\PaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ChambreController extends AbstractController
{
    private UrlGeneratorInterface $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }
    
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


            // //! Créer une session Stripe
            Stripe::setApiKey($_ENV['STRIPE_PRIVATE_KEY_TEST']);
            
            // $resStripe = [];
      
            $resStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $reservation->getChambre()->getTitre(),
                    ],
                    'unit_amount' => $reservation->getPrixTotal() * 100,
                ],
                'quantity' => 1
            ];
            // dd($reservation);

            $checkout_session = Session::create([
                'customer_email' => $reservation->getEmail(), 
                'payment_method_types' => ['card'], 
                'line_items' =>[[
                    $resStripe,
                ]],
                'mode' => 'payment',
                'success_url' => $this->generator->generate('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generator->generate('payment_error', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ]);
            
            $this->addFlash('success', 'Merci pour votre reservation! Vous avez reçu un email de confirmation.');

            return new RedirectResponse($checkout_session->url);


            return $this->redirectToRoute('chambre_index');

        }

        return $this->render('front/chambre/show.html.twig', [
            'formReservation' => $form,
            'chambre' => $chambre
        ]);
    }

    #[Route('/payment/success', name: 'payment_success')]
    public function stripeSuccess() :Response
    {
        
        return $this->redirectToRoute('chambre_index');
    }

    #[Route('/payment/error', name: 'payment_error')]
    public function stripeError() :Response
    {
        $this->addFlash('Danger', 'Payement non effectuer, veuillez réessayer !');
        return $this->redirectToRoute('chambre_index');
    }
}
