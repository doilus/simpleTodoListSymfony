<?php

namespace App\Entity;

use App\Repository\InvitationRepository;
use DateInterval;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InvitationRepository::class)
 */
class Invitation
{
    public const TIMESPAN = 15;
    public const STATUS_NEW = false;
    public const STATUS_ACCEPTED = true;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="invitations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $emailFriend;

    /**
     * @ORM\Column(type="datetime")
     */
    private $sentDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dueDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $statusRegistered;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    public function __construct(
        User $userId,
        string $emailFriend,
        string $token
    )
    {
        $this->userId = $userId;
        $this->emailFriend = $emailFriend;
        $this->token = $token;

        //others not from constructor
        $this->sentDate = new \DateTime();
        $this->dueDate = new \DateTime("+" . self::TIMESPAN . ' minutes');
        $this->statusRegistered = self::STATUS_NEW;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getEmailFriend(): ?string
    {
        return $this->emailFriend;
    }

    public function setEmailFriend(string $emailFriend): self
    {
        $this->emailFriend = $emailFriend;

        return $this;
    }

    public function getSentDate(): ?\DateTime
    {
        return $this->sentDate;
    }

    public function setSentDate(\DateTime $sentDate): self
    {
        $this->sentDate = $sentDate;

        return $this;
    }

    public function getDueDate(): ?\DateTime
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTime $dueDate): self
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function getStatusRegistered(): ?bool
    {
        return $this->statusRegistered;
    }

    public function setStatusRegistered(bool $statusRegistered): self
    {
        $this->statusRegistered = $statusRegistered;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function containsToken(string $token): bool
    {
        return (bool)($this->token === $token);
    }

    public function acceptInvitation(): void
    {
        $this->statusRegistered = self::STATUS_ACCEPTED;
    }

    public function isValid(): bool
    {
        $currentDate = new \DateTime();

        return(
            $currentDate < $this->dueDate || $this->statusRegistered === self::STATUS_ACCEPTED
        );
    }

    public function  isAccepted(): bool
    {
        return $this->statusRegistered === self::STATUS_ACCEPTED;
    }
}
