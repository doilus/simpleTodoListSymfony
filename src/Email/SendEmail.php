<?php


namespace App\Email;


use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class SendEmail
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(string $emailFrom, string $emailTo, string $emailSubject, string $templatePath, string $attachFilePath): void
    {
        $email = (new TemplatedEmail())
            ->from($emailFrom)
            ->to($emailTo)
            ->subject($emailSubject)
            ->htmlTemplate($templatePath)
            ->attachFromPath($attachFilePath);
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            //utworzenie nowego serwisu
        }
    }


}