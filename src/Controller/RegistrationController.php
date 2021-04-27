<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Messenger\Email\SendWelcomeEmail;
use App\Repository\InvitationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{

    private UserPasswordEncoderInterface $passwordEncoder;

    private InvitationRepository $invitationRepository;

    private UserRepository $userRepository;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        InvitationRepository $invitationRepository,
        UserRepository $userRepository
    )
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->invitationRepository = $invitationRepository;
        $this->userRepository = $userRepository;
    }

    #[Route('/register', name: 'app_register')]
    #[Route('/register/{token}', name: 'app_register_invitation')]
    public function register(Request $request, string $token = null, MessageBusInterface $messageBus): Response
    {
        if ($token) {
            $invitation = $this->invitationRepository->findOneBy([
                'token' => $token
            ]);


        if (!$invitation) {
            //todo: albo inny komunikat ze brakuje zaproszenia z danym tokenem
            return $this->render('invitation/invitation_late_register.html.twig');
        }

        if (!$invitation->isValid()) {
            //todo: inny komunikat o spóźnieniu
            return $this->render('invitation/invitation_late_register.html.twig');
        }

        if ($invitation->isAccepted()) {
            //todo: zaakceptowane wczesniej
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

            //uruchomienie messengera
            $message = new SendWelcomeEmail($user);
            $messageBus->dispatch($message);

            if ($token) {
                $invitation->acceptInvitation();
                $this->invitationRepository->save($invitation);
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

}
