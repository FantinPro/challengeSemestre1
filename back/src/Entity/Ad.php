<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\AdRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\GreaterThan;

#[ORM\Entity(repositoryClass: AdRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            paginationEnabled: true,
            paginationItemsPerPage: 20,
            security: 'is_granted("ROLE_PREMIUM")',
        ),
        new Get(
            normalizationContext: ['groups' => ['read:ad', 'read:ad:stats']],
            security: 'is_granted("ROLE_PREMIUM") and object.getOwner() == user',
        ),
        new Post(
            denormalizationContext: ['groups' => ['post:ad']],
            securityPostDenormalize: "is_granted('ROLE_PREMIUM') and object.getOwner() == user",
        ),
        new Put(
            denormalizationContext: ['groups' => ['put:ad']],
            security: "is_granted('ROLE_PREMIUM') and object.getOwner() == user and object.getStatus() != 'accepted'",
        ),
        new Patch(
            denormalizationContext: ['groups' => ['patch:ad']],
            security: "is_granted('ROLE_ADMIN')",
        ),
        new Delete(
            security: "is_granted('ROLE_PREMIUM') and object.getOwner() == user",
        )
    ],
    normalizationContext: ['groups' => ['read:ad']],
    denormalizationContext: ['groups' => ['write:ad']],
)]
class Ad
{

    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PAYED = 'payed';

    const STATUS = [
        self::STATUS_PENDING,
        self::STATUS_ACCEPTED,
        self::STATUS_REJECTED,
        self::STATUS_PAYED,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:ad'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    #[Groups(['read:ad', 'put:ad', 'post:ad'])]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    #[Groups(['read:ad', 'put:ad', 'post:ad'])]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:ad', 'put:ad', 'post:ad'])]
    private ?string $message = null;

    #[ORM\Column]
    #[Groups(['read:ad', 'put:ad', 'post:ad'])]
    #[GreaterThan(0)]
    private ?float $price = null;

    #[ORM\ManyToOne(inversedBy: 'ads')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:ad', 'post:ad'])]
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'ad', targetEntity: Stat::class)]
    private Collection $stats;

    #[ORM\Column(length: 30)]
    #[Choice(choices: self::STATUS)]
    #[Groups(['read:ad', 'patch:ad'])]
    private ?string $status = self::STATUS_PENDING;

    #[Timestampable(on: 'create')]
    #[ORM\Column(name: 'created', type: Types::DATETIME_MUTABLE)]
    #[Groups(['read:ad'])]
    private $created;

    #[ORM\Column(name: 'updated', type: Types::DATETIME_MUTABLE)]
    #[Timestampable(on: 'update')]
    #[Groups(['read:ad'])]
    private $updated;

    public function __construct()
    {
        $this->stats = new ArrayCollection();
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, Stat>
     */
    public function getStats(): Collection
    {
        return $this->stats;
    }

    public function addStat(Stat $stat): self
    {
        if (!$this->stats->contains($stat)) {
            $this->stats->add($stat);
            $stat->setAd($this);
        }

        return $this;
    }

    public function removeStat(Stat $stat): self
    {
        if ($this->stats->removeElement($stat)) {
            // set the owning side to null (unless already changed)
            if ($stat->getAd() === $this) {
                $stat->setAd(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
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

    #[Groups(['read:ad:stats'])]
    public function getImpressions() {
        return count($this->stats);
    }

    #[Groups(['read:ad:stats'])]
    public function getClicks() {
        return count(array_filter($this->stats->toArray(), function (Stat $stat) {
            return $stat->isClick();
        }));
    }

}
