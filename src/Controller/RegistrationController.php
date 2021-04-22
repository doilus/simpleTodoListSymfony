<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
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
    #[Route('/register', name: 'app_register')]
    public function register(MailerInterface $mailer, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            //send email
            //NamedAddress - *exists* symfony 4.4 or less
            $email = (new TemplatedEmail())
                ->from('alienmailer@example.com')
                ->to($user->getEmail())
                ->subject('Welcome to the task list!')
                ->htmlTemplate('email/welcome.html.twig')
                ->context([
                    'user' => $user
                ]);

            $mailer->send($email);


            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/register/{token}', name: 'app_register_invitation')]
    public function registerFromInvitation(SetInvitation $setInvitation, MailerInterface $mailer, Request $request, $token, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if ($setInvitation->verify($token)) {
            //return $this->redirectToRoute('app_register'); <-- jak przechować wartość tokena i emaila zeby nie szukac jeszcze raz | dla wszystkich
            $user = new User();
            $form = $this->createForm(RegistrationFormType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // encode the plain password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                // do anything else you need here, like send an email

                //send email
                //NamedAddress - *exists* symfony 4.4 or less
                $email = (new TemplatedEmail())
                    ->from('alienmailer@example.com')
                    ->to($user->getEmail())
                    ->subject('Welcome to the task list!')
                    ->htmlTemplate('email/welcome.html.twig')
                    ->context([
                        'user' => $user
                    ]);

                $mailer->send($email);

                $setInvitation->changeInvitationStatus($user->getEmail(), $token);  //jezeli zarejestrowal sie na ten sam mail


                return $this->redirectToRoute('app_login');
            }

            return $this->render('registration/register.html.twig', [
                'registrationForm' => $form->createView(),
            ]);
        }

        return $this->render('email/invitation/invitation_late_register.html.twig');
    }
}
