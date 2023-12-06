<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Chambre;
use App\Entity\Reservation;
use Stripe\Checkout\Session;
use App\Service\ReservationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    private UrlGeneratorInterface $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }
    
    #[Route('/payment/create-session-stripe/{id}', name: 'payment_stripe')]
    public function stripeCheckout($id, ReservationService $reservationService, Reservation $reservation, Chambre $chambre): RedirectResponse
    {
        $reservationInfo = $reservationService->reservationUser($reservation, $chambre);
        Stripe::setApiKey($_ENV['STRIPE_PRIVATE_KEY_TEST']);
        
        //! tableau vide
        $reservationStripe = [];
        
        
        //! parcourir le panier et remplir le tableau
        foreach($reservationInfo as $info){
            $reservationStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'amount' => $info['chambre']->getPrixTotal(),
                    'product_data' => [
                        'name' => $info['chambre']->getTitre(),
                        ]
                    ],
                    // 'quantity' => $info['prixTotal']
                ];
        }

       
        
        $checkout_session = Session::create([
            'customer_email' => $reservation->getEmail(), 
            'payment_method_types' => ['card'], 
            'line_items' =>[[
                $reservationStripe,
            ]],
          'mode' => 'payment',
          'success_url' => $this->generator->generate('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
          'cancel_url' => $this->generator->generate('payment_error', [], UrlGeneratorInterface::ABSOLUTE_URL)
      ]);
        
        return new RedirectResponse($checkout_session->url) ;
    }

    #[Route('/payment/success', name: 'payment_success')]
    public function stripeSuccess() :Response
    {
        return $this->redirectToRoute('chambre_index');
    }

    #[Route('/payment/error', name: 'payment_error')]
    public function stripeError() :Response
    {
        $this->addFlash('Danger', 'Payement non effectuer');
        return $this->redirectToRoute('chambre_index');
    }
}
