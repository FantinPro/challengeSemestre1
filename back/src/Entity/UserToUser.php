<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\UserToUserRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Post;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Choice;

#[ORM\Entity(repositoryClass: UserToUserRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_USER')",
        ),
        new Post(
            securityPostDenormalize: "is_granted('ROLE_USER') and object.getMe().getId() == user.getId()",
        ),
        new Delete(
            security: "is_granted('ROLE_USER') and object.getMe().getId() == user.getId()",
        ),
    ],
    normalizationContext: ['groups' => ['read:user_to_user']],
    denormalizationContext: ['groups' => ['write:user_to_user']]
)]
#[ApiFilter(SearchFilter::class, properties: ['me' => 'exact', 'other' => 'exact'])]
#[UniqueEntity(
    fields: ['me', 'other'],
    message: 'You already follow this user.',
    errorPath: 'other',
)]
class UserToUser
{

    const STATUS_FOLLOWING = 'following';
    const STATUS_BLOCKED = 'blocked';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:user_to_user'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'follows')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:user_to_user', 'write:user_to_user'])]
    private ?User $me = null;

    #[ORM\ManyToOne(inversedBy: 'followers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:user_to_user', 'write:user_to_user'])]
    private ?User $other = null;

    #[ORM\Column(length: 30)]
    #[Groups(['read:user_to_user', 'write:user_to_user'])]
    #[Choice(choices: ['following', 'blocked'], message: 'Choose a valid status.')]
    private string $status = 'following';

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
