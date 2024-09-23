<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;

class EmailNotificationService extends AbstractService
{
    public function sendEmail(string $receiver, string $case): ?string
    {
        try {
            $email = (new TemplatedEmail())
                ->from('hello@codexpress.fr')
                ->to($receiver)
                ;

                if ($case === 'premium') {
                    $email
                        ->subject('Thank you for your purchase!')
                        ->priority(Email::PRIORITY_HIGH)
                        ->htmlTemplate('email/premium.html.twig')
                        ;
                } elseif ($case === 'registration') {
                    $email
                        ->subject('Welcome to CodeXpress, explore a new way of sharing code')
                        ->htmlTemplate('email/welcome.html.twig')
                        ;
                }

    
            $this->mailer->send($email);
            return 'The e-mail was sucessfully sent!';
        } catch (\Exception $e) {
            return 'An error occurred while sending the e-mail: ' . $e->getMessage();
        }

    }
}