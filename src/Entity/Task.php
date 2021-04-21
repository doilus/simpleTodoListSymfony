<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
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
    private $title;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $slug;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dueDate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;


    /**
     * @ORM\Column(type="boolean")
     */
    private $isDone;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="taskId")
     */
    private $imagesId;



    public function __construct()
    {
        $this->imagesId = new ArrayCollection();
    }
    /*
    public function __construct(
        string $title,
        string $slug,
        \DateTimeInterface $dueDate,
        string $description,
        User $user,
        bool $isDone,
        string $imageFileName
    )
    {
        $this->title = $title;
        $this->slug = $slug;
        $this->dueDate = $dueDate;
        $this->description = $description;
        $this->user = $user;
        $this->isDone = $isDone;
        $this->imageFileName = $imageFileName;
    }*/


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(?\DateTimeInterface $dueDate): self
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIsDone(): ?bool
    {
        return $this->isDone;
    }

    public function setIsDone(bool $isDone): self
    {
        $this->isDone = $isDone;

        return $this;
    }

    public function checkDateReminder(): bool
    {
        $currentDate = new \DateTime;

        $days = $this->getDueDate()->diff($currentDate)->d;

        return ($days <= 3 && $days >= 0 && $this->getDueDate() > $currentDate);
    }

    public function isOutdated(): bool
    {
        $currentDate = new \DateTime;

        return ($this->getDueDate() < $currentDate);
    }


    /**
     * @return Collection|Image[]
     */

    public function getImagesId(): Collection
    {
        return $this->imagesId;
    }

    public function addImagesId(Image $imagesId): self
    {
        if (!$this->imagesId->contains($imagesId)) {
            $this->imagesId[] = $imagesId;
            $imagesId->setTaskId($this);
        }

        return $this;
    }

    public function removeImagesId(Image $imagesId): self
    {
        if ($this->imagesId->removeElement($imagesId)) {
            // set the owning side to null (unless already changed)
            if ($imagesId->getTaskId() === $this) {
                $imagesId->setTaskId(null);
            }
        }

        return $this;
    }


    /*
    public function getImageFileName()
    {
        return $this->imageFileName;
    }

    public function setImageFileName(?string $imageFileName)
    {
        $this->imageFileName = $imageFileName;

        return $this;
    }*/

}
