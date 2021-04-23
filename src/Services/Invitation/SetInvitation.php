<?php


namespace App\Services\Invitation;


use App\Email\SendInvitation;
use App\Entity\Invitation;
use App\Entity\User;
use App\Repository\InvitationRepository;

class SetInvitation
{
    private InvitationRepository $invitationRepository;
    private SendInvitation $sendInvitation;

    public function __construct(
        InvitationRepository $invitationRepository,
        SendInvitation $sendInvitation                  //trzymac sie odpowiedniej kolejnosci
    )
    {
        $this->invitationRepository = $invitationRepository;
        $this->sendInvitation = $sendInvitation;
    }

    public function setInvitation(User $userFrom, string $userFriendTo): void
    {
        $token = md5(uniqid(time()));   //zamiast random_bytes(10); <-- generuje randowmowy id w zaleznosci od czasu
                                        //uzylam hashowania md5, bo w bazie nie mogam zapsac stringa z '\' itp
        $invitation = new Invitation(
            $userFrom,
            $userFriendTo,
            $token
        );
        $this->invitationRepository->save($invitation);            //zapis - usuniecie obslugiwane w repo

        $this->sendInvitation->sendInvitation($invitation);
    }

    public function searchAcceptedInvitations(User $user): ?Invitation
    {
        $invitations = $this->invitationRepository->findOneBy([
            'userId' => $user,
            'statusRegistered' => true
        ]);



        return $invitations;
    }
}