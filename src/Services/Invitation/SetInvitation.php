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
        $this->sendInvitation = $sendInvitation;
        $this->invitationRepository = $invitationRepository;
    }

    public function setInvitation(User $userFrom, string $userFriendTo): void
    {
        $currentDate = new \DateTime();

        $invitation = new Invitation(
            $userFrom,
            $userFriendTo,
            $currentDate
        );

        $invitation->setDueDate();  //set date
        $invitation->setStatusRegistered(false);

        //generate url
        $random = random_bytes(10);

        $url = md5($random);

        $invitation->setUrlRandom($url);    //uzylam hashowania md5, bo w bazie nie mogam zapsac stringa z '\' itp

        $this->invitationRepository->save($invitation);


        $this->sendInvitation->sendInvitation($invitation);

    }

    public function changeInvitationStatus(string $email, $token): void
    {
        $invitation = $this->invitationRepository->findOneBy(['emailFriend' => $email]);

        if ($invitation->getUrlRandom() == $token) {
            $invitation->setStatusRegistered(true);
        }

    }

    public function verify($token): bool
    {
        $invitation = $this->invitationRepository->findOneBy(['urlRandom' => $token]);
        if ($invitation == null) {
            return false;
        }
        $currentDate = new \DateTime();
        if ($currentDate > $invitation->getDueDate()) {
            return false;
        }
        return true;
    }


}