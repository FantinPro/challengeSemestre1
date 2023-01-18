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
            security: "is_granted('ROLE_USER')",
        )
    ]
)]
class UserToUser
{

    const STATUS_FOLLOWING = 'following';
    const STATUS_BLOCKED = 'blocked';

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
        // if there is a user to user where other is me and me is other, and the status is blocked, return null
        if ($this->other->getFollows()->filter(fn(UserToUser $userToUser) => $userToUser->getMe() === $this->me && $userToUser->getStatus() === self::STATUS_BLOCKED)->count() > 0) {
            return null;
        }

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
