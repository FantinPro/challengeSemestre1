<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\UserToUserRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserToUserRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_USER') and request.get('me') == user.getId()",
        ),
        new Post(
            securityPostDenormalize: "is_granted('ROLE_USER') and object.getMe().getId() == user.getId()",
        )
    ],
    normalizationContext: ['groups' => ['read:user_to_user_read']],
    denormalizationContext: ['groups' => ['write:user_to_user']]
)]
#[ApiFilter(SearchFilter::class, properties: ['me' => 'exact'])]
class UserToUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:user_to_user_read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'follows')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $me = null;

    #[ORM\ManyToOne(inversedBy: 'followers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:user_to_user_read'])]
    private ?User $other = null;

    #[ORM\Column(length: 30)]
    #[Groups(['read:user_to_user_read'])]
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
