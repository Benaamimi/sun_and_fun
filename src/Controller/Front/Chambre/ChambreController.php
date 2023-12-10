<?php

namespace App\Controller\Front\Chambre;

use Stripe\Stripe;
use App\Entity\Chambre;
use App\Entity\Reservation;
use Stripe\Checkout\Session;
use App\Form\ReservationType;
use App\Service\MailerService;
use App\Repository\ChambreRepository;
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
    public function show(Chambre $id, 
                    Request $request, 
                    ChambreRepository $chambreRepository, 
                    EntityManagerInterface $em, 
                    MailerService $mailerService, 
                ): Response
    {
        $reservation = new Reservation;
        $chambre = $chambreRepository->find($id);

        if(!$chambre){
            $this->addFlash('warning', 'Veuillez reservez une autre chambre');
        }
        elseif(!$reservation){
            $this->addFlash('warning', 'Veuillez refaire la reservation');
        }
        
        $form = $this->createForm(ReservationType::class, $reservation, ['chambre' => false]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
           
            //! Reservation de chambre avec injection de dependance
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

            //! actualiser en base de données
            $em->persist($reservation);
            $em->flush();
            

            //! envoie de mail Utilisateur
            $mailerService->sendMailToUser($reservation->getEmail(), $reservation);

            //! envoie de mail Admin
            $mailerService->sendMailToAdmin('sunandfun.chambre@gmail.com', $reservation);


            // //! Créer une session Stripe
            Stripe::setApiKey($_ENV['STRIPE_PRIVATE_KEY_TEST']);

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
            
            return new RedirectResponse($checkout_session->url);

        }

        return $this->render('front/chambre/show.html.twig', [
            'formReservation' => $form,
            'chambre' => $chambre
        ]);
    }

    #[Route('/payment/success', name: 'payment_success')]
    public function stripeSuccess() :Response
    {
        $this->addFlash('success', 'Merci pour votre reservation! Vous avez reçu un email de confirmation.');
        return $this->redirectToRoute('chambre_index');
    }

    #[Route('/payment/error', name: 'payment_error')]
    public function stripeError() :Response
    {
        $this->addFlash('Danger', 'Payement non effectuer, veuillez réessayer !');
        return $this->redirectToRoute('chambre_index');
    }
}
