<?php


namespace App\MessageHandler;


use App\Entity\User;
use App\Messenger\Email\SendWelcomeEmail;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Message;

class SendWelcomeEmailHandler implements MessageHandlerInterface
{
    private MailerInterface $mailer;
    public function __construct(
        MailerInterface $mailer
    )
    {
        $this->mailer = $mailer;
    }

    public function __invoke(SendWelcomeEmail $sendWelcomeEmail)
    {
        $user = $sendWelcomeEmail->getUser();

        $email = (new TemplatedEmail())
            ->from('alienmailer@example.com')
            ->to($user->getEmail())
            ->subject('Welcome to the task list!')
            ->htmlTemplate('email/welcome.html.twig')
            ->context([
                'user' => $user
            ]);

        $this->mailer->send($email);

    }

}