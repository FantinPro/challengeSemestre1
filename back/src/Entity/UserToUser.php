<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserToUserRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserToUserRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            securityPostDenormalize: "is_granted('ROLE_USER') and object.getMe().getId() == user.getId()",
        )
    ]
)]
class UserToUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'follows')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $me = null;

    #[ORM\ManyToOne(inversedBy: 'followers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $other = null;

    #[ORM\Column(length: 30)]
    private ?string $status = 'following';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMe(): ?User
    {
        return $this->me;
    }

    public function setMe(?User $me): self
    {
        $this->me = $me;

        return $this;
    }

    public function getOther(): ?User
    {
        return $this->other;
    }

    public function setOther(?User $other): self
    {
        $this->other = $other;

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
