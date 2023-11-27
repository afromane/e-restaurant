<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail($subject, $recipient, $body)
    {
        $email = (new Email())
            ->from('your-email@gmail.com')
            ->to($recipient)
            ->subject($subject)
            ->text($body);

        $this->mailer->send($email);
    }
}