<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\FeedController;
use App\Controller\MessageWithAtLeast2ReportsController;
use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/messages/feed',
            controller: FeedController::class,
            normalizationContext: ['groups' => ['read:message', 'read:message:feed']],
            security: 'is_granted("ROLE_USER")',
        ),
        new GetCollection(
            uriTemplate: '/messages/reports',
            controller: MessageWithAtLeast2ReportsController::class,
            normalizationContext: ['groups' => ['read:message', 'read:message:reports']],
            security: 'is_granted("ROLE_MODERATOR")',
        ),
        new GetCollection(
            paginationEnabled: true,
            paginationItemsPerPage: 20,
            normalizationContext: ['groups' => ['read:message:search']],
        ),
        new Get(
            security: "is_granted('ROLE_USER')",
        ),
        new Post(
            securityPostDenormalize: 'is_granted("ROLE_USER") and object.getCreator() == user',
            securityPostDenormalizeMessage: 'You can only create messages for yourself.',
        ),
        new Put(
            securityPostDenormalize: 'is_granted("ROLE_USER") and object.creator.getId() == user.getId()',
            securityPostDenormalizeMessage: 'You can only update messages for yourself.',
        ),
        new Delete(
            security: 'is_granted("ROLE_USER") and object.getCreator() == user',
        ),
        new Patch(
            denormalizationContext: ['groups' => ['patch:message']],
            security: 'is_granted("ROLE_MODERATOR")',
        )
    ],
    normalizationContext: ['groups' => ['read:message']],
    denormalizationContext: ['groups' => ['write:message']],
)]
#[ApiFilter(SearchFilter::class, properties: ['content' => 'ipartial', 'creator' => 'exact'])]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:message:feed'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:message', 'read:message:feed', 'write:message', 'read:message:search'])]
    private ?User $creator;

    #[Groups(['read:message', 'write:message', 'read:message:search'])]
    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'messagesSharedByUser')]
    private Collection $usersSharingMessage;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'comments')]
    private ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ['remove'])]
    private Collection $comments;

    #[ORM\Column]
    #[Groups(['read:message', 'patch:message'])]
    private ?bool $isDeleted = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $editedAt = null;

    #[ORM\OneToMany(mappedBy: 'reportedMessage', targetEntity: Report::class)]
    #[Groups(['read:message:reports'])]
    private Collection $reports;

    #[Timestampable(on: 'create')]
    #[ORM\Column(name: 'created', type: Types::DATETIME_MUTABLE)]
    #[Groups(['read:message'])]
    private $created;

    #[ORM\Column(name: 'updated', type: Types::DATETIME_MUTABLE)]
    #[Timestampable(on: 'update')]
    #[Groups(['read:message'])]
    private $updated;

    #[ORM\OneToMany(mappedBy: 'sharedMessage', targetEntity: Share::class)]
    private Collection $shares;

    public function __construct()
    {
        $this->usersSharingMessage = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
        $this->shares = new ArrayCollection();
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
            return 'this message has been deleted because it does not respect the rules of the application';
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

    #[Groups(['read:message:reports'])]
    public function getReportsCount(): int
    {
        return count($this->reports);
    }

    #[Groups(['read:message:feed'])]
    public function getCommentsCount(): int
    {
        return count($this->comments);
    }

    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): \DateTime
    {
        return $this->updated;
    }

    public function setUpdated(\DateTime $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * @return Collection<int, Share>
     */
    public function getShares(): Collection
    {
        return $this->shares;
    }

    public function addShare(Share $share): self
    {
        if (!$this->shares->contains($share)) {
            $this->shares->add($share);
            $share->setSharedMessage($this);
        }

        return $this;
    }

    public function removeShare(Share $share): self
    {
        if ($this->shares->removeElement($share)) {
            // set the owning side to null (unless already changed)
            if ($share->getSharedMessage() === $this) {
                $share->setSharedMessage(null);
            }
        }

        return $this;
    }
}
