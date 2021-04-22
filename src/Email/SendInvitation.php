<?php


namespace App\Email;


use App\Entity\Invitation;
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

    public function sendInvitation(Invitation $invitation): void
    {
        $templatePath = 'email/invitation/invitation_email_template.html.twig';

        $userEmail = $invitation->getUserId()->getEmail();

        $timeLeft = $invitation->getSentDate()->diff($invitation->getDueDate())->i;

        $email = (new TemplatedEmail())
            ->from('alien@examle.eu')
            ->to($invitation->getEmailFriend())
            ->subject('Invitation')
            ->htmlTemplate($templatePath)
            ->context([
                'friendEmail' => $userEmail,
                'invitation' => $invitation,
                'timeLeft' => $timeLeft
            ])
        ;

        $this->mailerInterface->send($email);
    }


}