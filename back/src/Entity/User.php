<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use App\Controller\UserController;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\Post;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    operations: [
        new Post(),
        new Put(),
    ],
    normalizationContext: ['groups' => ['read:user']],
    denormalizationContext: ['groups' => ['write:user']],
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:user'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(message: 'Invalid email address')]
    #[Groups(['write:user', 'read:user'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['read:user'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['write:user'])]
    private ?string $password = null;

    #[ORM\Column(
        options: [
            'default' => false
        ]
    )]
    private ?bool $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'fromUser', targetEntity: TokenResetPassword::class, orphanRemoval: true)]
    private Collection $tokenResetPasswords;

    #[ORM\Column(nullable: true)]
    private ?bool $isPremium = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilePicture = null;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $username = null;

    #[ORM\OneToMany(mappedBy: 'me', targetEntity: UserToUser::class)]
    private Collection $follows;

    #[ORM\OneToMany(mappedBy: 'other', targetEntity: UserToUser::class)]
    private Collection $followers;

    public function __construct()
    {
        $this->tokenResetPasswords = new ArrayCollection();
        $this->follows = new ArrayCollection();
        $this->followers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, TokenResetPassword>
     */
    public function getTokenResetPasswords(): Collection
    {
        return $this->tokenResetPasswords;
    }

    public function addTokenResetPassword(TokenResetPassword $tokenResetPassword): self
    {
        if (!$this->tokenResetPasswords->contains($tokenResetPassword)) {
            $this->tokenResetPasswords->add($tokenResetPassword);
            $tokenResetPassword->setFromUser($this);
        }

        return $this;
    }

    public function removeTokenResetPassword(TokenResetPassword $tokenResetPassword): self
    {
        if ($this->tokenResetPasswords->removeElement($tokenResetPassword)) {
            // set the owning side to null (unless already changed)
            if ($tokenResetPassword->getFromUser() === $this) {
                $tokenResetPassword->setFromUser(null);
            }
        }

        return $this;
    }

    public function isIsPremium(): ?bool
    {
        return $this->isPremium;
    }

    public function setIsPremium(bool $isPremium): self
    {
        $this->isPremium = $isPremium;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection<int, UserToUser>
     */
    public function getFollows(): Collection
    {
        return $this->follows;
    }

    public function addFollow(UserToUser $follow): self
    {
        if (!$this->follows->contains($follow)) {
            $this->follows->add($follow);
            $follow->setMe($this);
        }

        return $this;
    }

    public function removeFollow(UserToUser $follow): self
    {
        if ($this->follows->removeElement($follow)) {
            // set the owning side to null (unless already changed)
            if ($follow->getMe() === $this) {
                $follow->setMe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserToUser>
     */
    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function addFollower(UserToUser $follower): self
    {
        if (!$this->followers->contains($follower)) {
            $this->followers->add($follower);
            $follower->setOther($this);
        }

        return $this;
    }

    public function removeFollower(UserToUser $follower): self
    {
        if ($this->followers->removeElement($follower)) {
            // set the owning side to null (unless already changed)
            if ($follower->getOther() === $this) {
                $follower->setOther(null);
            }
        }

        return $this;
    }
}
