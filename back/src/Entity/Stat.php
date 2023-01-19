<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\StatRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: StatRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            security: "is_granted('ROLE_USER') and object.getFromUser() == user",
        ),
        new Patch(
            denormalizationContext: ['groups' => ['patch:stat']],
            securityPostDenormalize: "is_granted('ROLE_USER') and object.getFromUser() == user",
        ),
    ],
    normalizationContext: ['groups' => ['read:stat']],
    denormalizationContext: ['groups' => ['write:stat']],
)]
#[UniqueEntity(
    fields: ['ad', 'fromUser'],
    message: 'You already see the ad.',
    errorPath: 'fromUser',
)]
class Stat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['patch:stat'])]
    private ?bool $click = null;

    #[ORM\ManyToOne(inversedBy: 'stats')]
    private ?Ad $ad = null;

    #[ORM\ManyToOne(inversedBy: 'stats')]
    private ?User $fromUser = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isClick(): ?bool
    {
        return $this->click;
    }

    public function setClick(?bool $click): self
    {
        $this->click = $click;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getFromUser(): ?User
    {
        return $this->fromUser;
    }

    public function setFromUser(?User $fromUser): self
    {
        $this->fromUser = $fromUser;

        return $this;
    }
}
