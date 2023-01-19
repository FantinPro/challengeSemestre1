<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
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
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\GreaterThan;

#[ORM\Entity(repositoryClass: AdRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(
            security: 'is_granted("ROLE_PREMIUM") and object.getOwner() == user',
        ),
        new Post(
            denormalizationContext: ['groups' => ['post:ad']],
            securityPostDenormalize: "is_granted('ROLE_PREMIUM') and object.getOwner() == user",
        ),
        new Put(
            denormalizationContext: ['groups' => ['put:ad']],
            security: "is_granted('ROLE_PREMIUM') and object.getOwner() == user",
        ),
        new Patch(
            denormalizationContext: ['groups' => ['patch:ad']],
            security: "is_granted('ROLE_ADMIN')",
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

    const STATUS = [
        self::STATUS_PENDING,
        self::STATUS_ACCEPTED,
        self::STATUS_REJECTED,
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

    public function __construct()
    {
        $this->stats = new ArrayCollection();
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

}
