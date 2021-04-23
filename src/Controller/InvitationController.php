<?php


namespace App\Controller;


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
            $loggedUser = $this->getUser();

            $this->setInvitation->setInvitation(
                $loggedUser,        //mozna zignorowac  w phstormie - symfony nie wyrzuca błedu
                $recipient
            );

            return $this->redirectToRoute('invitation_send_success');
        }

        return $this->render('invitation/invitation_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/sendInvitation/success", name="invitation_send_success", methods={"GET"})
     */
    public function infoSuccessInvitation(): Response
    {

        //todo: strona z listą zaproszeń

        $this->addFlash('emailSent', 'yey!');

        return $this->render('invitation/invitation_success.html.twig');
    }

    /**
     * @Route("/invitation/list", name="invitation_list", methods={"GET"})
     */
    public function invintationList(): Response
    {
        $invitations[] = $this->setInvitation->searchAcceptedInvitations($this->getUser());
        //dd($invitations);
        return $this->render('invitation/show_list_invitation.html.twig', [
            'invitations' => $invitations
        ]);
    }

}