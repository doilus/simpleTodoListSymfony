<?php


namespace App\Messenger\Email;


use App\Entity\User;

class SendWelcomeEmail
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

}