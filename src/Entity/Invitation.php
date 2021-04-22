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
    private $urlRandom;

    public function __construct(
        User $userId,
        string $emailFriend,
        \DateTime $sentDate
    )
    {
        $this->userId = $userId;
        $this->emailFriend = $emailFriend;
        $this->sentDate = $sentDate;

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

    public function setDueDate(): self
    {
        $timespan = 15;
        $this->dueDate = new \DateTime($this->sentDate->format('Y-m-d H:i'));
        $this->dueDate->add(new DateInterval('PT' . $timespan . 'M'));

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

    public function getUrlRandom(): ?string
    {
        return $this->urlRandom;
    }

    public function setUrlRandom(string $urlRandom): self
    {
        $this->urlRandom = $urlRandom;

        return $this;
    }
}
