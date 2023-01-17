<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $creator = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'messagesSharedByUser')]
    private Collection $usersSharingMessage;

    public function __construct()
    {
        $this->usersSharingMessage = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsersSharingMessage(): Collection
    {
        return $this->usersSharingMessage;
    }

    public function addUsersSharingMessage(User $usersSharingMessage): self
    {
        if (!$this->usersSharingMessage->contains($usersSharingMessage)) {
            $this->usersSharingMessage->add($usersSharingMessage);
        }

        return $this;
    }

    public function removeUsersSharingMessage(User $usersSharingMessage): self
    {
        $this->usersSharingMessage->removeElement($usersSharingMessage);

        return $this;
    }
}
