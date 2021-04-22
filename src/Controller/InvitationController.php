<?php


namespace App\Controller;


use App\Entity\User;
use App\Form\Type\InvitationType;
use App\Repository\UserRepository;
use App\Services\Invitation\SetInvitation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvitationController extends AbstractController
{
    private SetInvitation $setInvitation;

    private UserRepository $userRepository;

    public function __construct(
        SetInvitation $setInvitation,
        UserRepository $userRepository
    )
    {
        $this->setInvitation = $setInvitation;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/sendInvitation", name="send_invitation", methods={"GET", "POST"})
     */
    public function createInvitation(Request $request): Response
    {
        $form = $this->createForm(InvitationType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipient = $form['email']->getData();

            /** @var User $loggedUser */
            $loggedUser = $this->getUser();

            $this->setInvitation->setInvitation(
                $loggedUser,
                $recipient
            );

            return $this->redirectToRoute('invitation_send_success');
        }

        return $this->render('invitation/invitation_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/sendInvitation/success", name="invitation_send_success")
     */
    public function infoSuccessInvitation(): Response
    {
        //todo: do zastanowienia się czy nie możemy zrobic jakieś strony z listą zaproszeń

        $this->addFlash('emailSent', 'yey!');

        return $this->render('invitation/invitation_success.html.twig');
    }

}