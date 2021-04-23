<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\InvitationRepository;
use App\Repository\UserRepository;
use App\Services\Invitation\SetInvitation;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    private SetInvitation $setInvitation;

    private MailerInterface $mailer;

    private UserPasswordEncoderInterface $passwordEncoder;

    private InvitationRepository $invitationRepository;

    private UserRepository $userRepository;

    public function __construct(
        SetInvitation $setInvitation,
        MailerInterface $mailer,
        UserPasswordEncoderInterface $passwordEncoder,
        InvitationRepository $invitationRepository,
        UserRepository $userRepository
    )
    {
        $this->setInvitation = $setInvitation;
        $this->mailer = $mailer;
        $this->passwordEncoder = $passwordEncoder;
        $this->invitationRepository = $invitationRepository;
        $this->userRepository = $userRepository;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, string $token = null): Response
    {

        //zabezpieczenie przed nieistniejacym tokenem
        if($token){
            $invitation = $this->invitationRepository->findOneBy([
               'token' => $token
            ]);
        }

        if(!$invitation){
            //todo: albo inny komunikat ze brakuje zaproszenia z danym tokenem
            return $this->render('invitation/invitation_late_register.html.twig');
        }

        if(!$invitation->isValid()){
            //todo: inny komunikat o spóźnieniu
            return $this->render('invitation/invitation_late_register.html.twig');
        }

        if($invitation->isAccepted()){
            //todo: zaakceptowane wczesniej
            return $this->render('invitation/invitation_late_register.html.twig');
        }

        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           $this->prepareUserPassword(
               $user,
               $form->get('plainPassword')->getData()
           );

            $this->userRepository->save($user);

            $this->sendWelcomeEmail($user);

            if($token){
                $invitation->acceptInvitation();
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    private function prepareUserPassword(User $user, string $plainPassword): void
    {
        // encode the plain password
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user,
                $plainPassword
            )
        );
    }

    private function sendWelcomeEmail(User $user): void
    {
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
