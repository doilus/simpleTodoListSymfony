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
        UserPasswordEncoderInterface $passwordEncoder,
        MailerInterface $mailer,
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
    #[Route('/register/{token}', name: 'app_register_invitation')]
    public function index(Request $request, string $token = null): Response
    {
        if ($token) {
            $invitation = $this->invitationRepository->findOneBy([
                'urlRandom' => $token
            ]);

            if (!$invitation) {
                //todo: albo inny komunikat, brak zaproszneia z tokenem
                return $this->render('invitation/invitation_late_register.html.twig');
            }

            if (!$invitation->isValid()) {
                //todo: albo inny komunikat, ze jesteś spóźniony
                return $this->render('invitation/invitation_late_register.html.twig');
            }

            if ($invitation->isAccepted()) {
                //todo: zaakceptowane wcześniej
                return $this->render('invitation/invitation_late_register.html.twig');
            }
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

            if ($token) {
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
