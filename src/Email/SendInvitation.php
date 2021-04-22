<?php


namespace App\Email;


use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class SendInvitation
{
    private MailerInterface $mailerInterface;

    public function __construct(MailerInterface $mailerInterface
    )
    {
        $this->mailerInterface = $mailerInterface;
    }

    public function sendInvitation(string $emailFrom, string $friendEmail, string $emailTo): void
    {
        $templatePath = 'email/invitation/invitation_email_template.html.twig';
        $email = (new TemplatedEmail())
            ->from($emailFrom)
            ->to($emailTo)
            ->subject('Invitation')
            ->htmlTemplate($templatePath)
            ->context([
                'friendEmail' => $friendEmail
            ])
        ;

        $this->mailerInterface->send($email);
    }


}