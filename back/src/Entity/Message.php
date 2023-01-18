<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('ROLE_USER')",
        ),
        new Post(
            securityPostDenormalize: 'is_granted("ROLE_USER") and object.creator.getId() == user.getId()',
            securityPostDenormalizeMessage: 'You can only create messages for yourself.',
        ),
        new Put(
            securityPostDenormalize: 'is_granted("ROLE_USER") and object.creator.getId() == user.getId()',
            securityPostDenormalizeMessage: 'You can only update messages for yourself.',
        ),
    ],
    normalizationContext: ['groups' => ['read:message']],
    denormalizationContext: ['groups' => ['write:message']],
)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $creator;

    #[Groups(['read:message', 'write:message'])]
    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'messagesSharedByUser')]
    private Collection $usersSharingMessage;

    #[ORM\OneToMany(mappedBy: 'reportedMessage', targetEntity: Report::class)]
    private Collection $reports;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'comments')]
    private ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    #[Groups(['read:message'])]
    private Collection $comments;

    #[ORM\Column]
    private ?bool $isDeleted = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $editedAt = null;

    public function __construct()
    {
        $this->usersSharingMessage = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->comments = new ArrayCollection();
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
        if($this->isDeleted) {
            return 'This message has been deleted.';
        }
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

    /**
     * @return Collection<int, Report>
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
            $report->setReportedMessage($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getReportedMessage() === $this) {
                $report->setReportedMessage(null);
            }
        }

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(self $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setParent($this);
        }

        return $this;
    }

    public function removeComment(self $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getParent() === $this) {
                $comment->setParent(null);
            }
        }

        return $this;
    }

    public function isIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function getEditedAt(): ?\DateTimeInterface
    {
        return $this->editedAt;
    }

    public function setEditedAt(): self
    {
        $this->editedAt = new \DateTime();

        return $this;
    }
}
