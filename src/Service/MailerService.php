<?php

namespace App\Service;

use App\Entity\Reservation;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendMailToUser(string $userEmail, $reservation)
    {
         //! envoie de mail à l'utilisateur
         $email = (new TemplatedEmail())
         ->from('sunandfun.noreply@gmail.com')
         ->to($userEmail)
         ->subject('Comfirmation de reservation')
         ->htmlTemplate('email/user_message.html.twig')
         ->context([
            'reservation' => $reservation
        ]);

         $this->mailer->send($email);
    }

    public function sendMailToAdmin(string $adminEmail, $reservation)
    {
        //! envoie un mail à l'admin
        $email = (new TemplatedEmail())
        ->from('sunandfun.noreply@gmail.com')
        ->to($adminEmail)
        ->subject('Nouvelle reservation effectuer')
        ->htmlTemplate('email/admin_message.html.twig')
        ->context([
            'reservation' => $reservation
        ]);

        $this->mailer->send($email);
    }


}