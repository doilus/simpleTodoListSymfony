<?php


namespace App\Email;


use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;

class SendEmail
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail($emailFrom, $emailTo, $emailSubject, $template, $attachFile ){
        $email = (new TemplatedEmail())
            ->from($emailFrom)
            ->to($emailTo)
            ->subject($emailSubject)
            ->htmlTemplate($template)
            ->attachFromPath($attachFile)
            ;
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }



}