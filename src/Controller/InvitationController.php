<?php


namespace App\Controller;


use App\Email\SendInvitation;
use App\Form\Type\InvitationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvitationController extends AbstractController
{
    private SendInvitation $sendInvitation;
    public function __construct(SendInvitation $sendInvitation)
    {
        $this->sendInvitation = $sendInvitation;
    }

    /**
     * @Route("/sendInvitation", name="send_invitation", methods={"GET", "POST"})
     */
    public function createInvitation(Request $request): Response{

        $form = $this->createForm(InvitationType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $invitationTo = $form['email']->getData();

            //wygeneruj linka do rejestracji
            //przypisz go do zmiennej i podaj tu do maila jako context

            //wyslij maila z zaproszeniem
            //funkcja
            $this->sendInvitation->sendInvitation('alien@alien.eu', $this->getUser()->getUsername(), $invitationTo);




            //daj znac ze zaproszenie zostalo wyslane
            return $this->redirectToRoute('invitation_send_success');
        }

        return $this->render('email/invitation/invitation_form.html.twig',
            ['form' => $form->createView()
            ]);
    }

    /**
     * @Route("/sendInvitation/success", name="invitation_send_success")
     */
    public function infoSuccessInvitation(): Response{
        return $this->render('email/invitation/invitation_success.html.twig');
    }

}