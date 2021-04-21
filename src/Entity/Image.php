<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $clientName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $createdName;

    /**
     * @ORM\Column(type="integer")
     */
    private $size;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $imagePath;

    /**
     * @ORM\ManyToOne(targetEntity=Task::class, inversedBy="imagesId", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $taskId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageName;

    public function __construct(
        string $clientName,
        string $createdName,
        string $imagePath,
        Task $taskId,
        int $size
    )
    {
        $this->clientName = $clientName;
        $this->createdName = $createdName;
        $this->imagePath = $imagePath;
        $this->taskId = $taskId;
        $this->size = $size;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClientName(): ?string
    {
        return $this->clientName;
    }

    public function setClientName(string $clientName): self
    {
        $this->clientName = $clientName;

        return $this;
    }

    public function getCreatedName(): ?string
    {
        return $this->createdName;
    }

    public function setCreatedName(string $createdName): self
    {
        $this->createdName = $createdName;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;
        // $this->size = filesize($this->imagePath . '/' . $this->createdName);

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath): self
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function getTaskId(): ?Task
    {
        return $this->taskId;
    }

    public function setTaskId(?Task $taskId): self
    {
        $this->taskId = $taskId;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getOfficialDestination(): string
    {
        return $this->imagePath . '/' . $this->createdName;
    }

    public function getClientNameWithExtension(){

        return $this->clientName . '.' .  pathinfo($this->createdName, PATHINFO_EXTENSION);
    }




}
