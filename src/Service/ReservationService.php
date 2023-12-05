<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReservationService extends AbstractController
{
    public function reservationAdmin($reservation)
    {
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
    }

    public function reservationUser($reservation, $chambre)
    {
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
            
    }
}