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
        SendInvitation $sendInvitation
    )
    {
        $this->invitationRepository = $invitationRepository;
        $this->sendInvitation = $sendInvitation;
    }


    public function setInvitation(User $userFrom, string $userFriendTo): void
    {
        $token = md5(uniqid(time()));

        $invitation = new Invitation(
            $userFrom,
            $userFriendTo,
            $token
        );

        $this->invitationRepository->save($invitation);

        $this->sendInvitation->sendInvitation($invitation);
    }
}