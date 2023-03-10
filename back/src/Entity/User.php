<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use App\Controller\FollowersController;
use App\Controller\FollowingController;
use App\Controller\MeController;
use App\Controller\UserSuggestionsController;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\Post;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/users/followers',
            controller: FollowersController::class,
            paginationEnabled: true,
            paginationItemsPerPage: 2,
            normalizationContext: ['groups' => ['read:user', 'read:user:follow']],
            security: "is_granted('ROLE_USER')",
        ),
        new GetCollection(
            uriTemplate: '/users/follows',
            controller: FollowingController::class,
            paginationEnabled: true,
            paginationItemsPerPage: 20,
            normalizationContext: ['groups' => ['read:user', 'read:user:follow']],
            security: "is_granted('ROLE_USER')",
        ),
        new GetCollection(
            uriTemplate: '/users/suggestions',
            controller: UserSuggestionsController::class,
            normalizationContext: ['groups' => ['read:user']],
            security: "is_granted('ROLE_USER')",
        ),
        new Post(),
        new Put(
            uriTemplate: '/users/{id}/change_role',
            denormalizationContext: ['groups' => ['put:user:change_role']],
            securityPostDenormalize: "is_granted('ROLE_ADMIN')",
        ),
        new Put(
            denormalizationContext: ['groups' => ['write:user']],
            security: 'is_granted("ROLE_USER") and object == user',
        ),
        new Get(
            uriTemplate: '/users/profile',
            controller: MeController::class,
            normalizationContext: ['groups' => ['read:user', 'read:user:profile']],
            security: "is_granted('ROLE_USER')",
            read: false,
        ),
        new Get(
            security: "is_granted('ROLE_USER')"
        ),
        new GetCollection(
            paginationEnabled: true,
            paginationItemsPerPage: 20,
            normalizationContext: ['groups' => ['read:user', 'read:users:collection']],
            security: "is_granted('ROLE_USER')",
        ),
    ],

    normalizationContext: ['groups' => ['read:user']],
    denormalizationContext: ['groups' => ['write:user']],
)]
#[ApiFilter(SearchFilter::class, properties: ['pseudo' => 'ipartial'])]
#[ApiFilter(OrderFilter::class, properties: ['created'], arguments: ['orderParameterName' => 'order'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_MODERATOR = 'ROLE_MODERATOR';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_PREMIUM = 'ROLE_PREMIUM';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:user', 'read:user_to_user','read:message:feed', 'read:message', 'read:message:search', 'read:ad'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(message: 'Invalid email address')]
    #[Groups(['write:user', 'read:message', 'read:users:collection'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['read:user', 'read:ad', 'read:message', 'read:message:search', 'read:user:search', 'read:message:feed', 'put:user:change_role', 'read:user_to_user'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['write:user'])]
    private ?string $password = null;

    #[Groups(['read:ad:random'])]
    #[ORM\Column(
        options: [
            'default' => false
        ]
    )]
    private ?bool $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'fromUser', targetEntity: TokenResetPassword::class, orphanRemoval: true)]
    private Collection $tokenResetPasswords;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:user', 'read:ad', 'read:ad:random', 'write:user', 'read:user_to_user_read', 'read:message', 'read:message:search', 'read:user:search', 'read:message:feed', 'read:user_to_user'])]
    private ?string $profilePicture = null;

    #[ORM\Column(length: 25, unique: true)]
    #[Groups(['read:user', 'read:ad', 'read:ad:random', 'write:user', 'read:user_to_user', 'read:message:feed', 'read:message', 'read:message:search', 'read:user:search'])]
    private ?string $pseudo = null;

    #[ORM\OneToMany(mappedBy: 'me', targetEntity: UserToUser::class)]
    private Collection $follows;

    #[ORM\OneToMany(mappedBy: 'other', targetEntity: UserToUser::class)]
    private Collection $followers;

    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: Message::class)]
    private Collection $messages;

    #[ORM\ManyToMany(targetEntity: Message::class, mappedBy: 'usersSharingMessage')]
    private Collection $messagesSharedByUser;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Pub::class)]
    private Collection $ads;

    #[ORM\OneToMany(mappedBy: 'reportingUser', targetEntity: Report::class)]
    private Collection $reports;

    #[ORM\OneToMany(mappedBy: 'fromUser', targetEntity: Stat::class)]
    private Collection $stats;

    #[Timestampable(on: 'create')]
    #[ORM\Column(name: 'created', type: Types::DATETIME_MUTABLE)]
    #[Groups(['read:user'])]
    private $created;

    #[ORM\Column(name: 'updated', type: Types::DATETIME_MUTABLE)]
    #[Timestampable(on: 'update')]
    private $updated;

    #[ORM\OneToMany(mappedBy: 'sharingBy', targetEntity: Share::class)]
    private Collection $shares;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:user', 'write:user', 'read:user_to_user', 'read:message:feed', 'read:message', 'read:message:search', 'read:user:search'])]
    private ?string $bio = null;

    #[Groups(['read:user:profile', 'read:user:follow'])]
    public bool $followed = false;

    #[Groups(['read:user:follow'])]
    public mixed $userToUserId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripeCustomerId = null;

    public function __construct()
    {
        $this->tokenResetPasswords = new ArrayCollection();
        $this->follows = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->messagesSharedByUser = new ArrayCollection();
        $this->ads = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->stats = new ArrayCollection();
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
        $this->shares = new ArrayCollection();
        $random = random_int(1, 100);
        $this->profilePicture = "https://avatars.dicebear.com/api/male/$random.svg";
        $this->setRoles([self::ROLE_USER]);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
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

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

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

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setCreator($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getCreator() === $this) {
                $message->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessagesSharedByUser(): Collection
    {
        return $this->messagesSharedByUser;
    }

    public function addMessagesSharedByUser(Message $messagesSharedByUser): self
    {
        if (!$this->messagesSharedByUser->contains($messagesSharedByUser)) {
            $this->messagesSharedByUser->add($messagesSharedByUser);
            $messagesSharedByUser->addUsersSharingMessage($this);
        }

        return $this;
    }

    public function removeMessagesSharedByUser(Message $messagesSharedByUser): self
    {
        if ($this->messagesSharedByUser->removeElement($messagesSharedByUser)) {
            $messagesSharedByUser->removeUsersSharingMessage($this);
        }

        return $this;
    }


    /**
     * @return Collection<int, Pub>
     */
    public function getAds(): Collection
    {
        return $this->ads;
    }

    public function addAd(Pub $ad): self
    {
        if (!$this->ads->contains($ad)) {
            $this->ads->add($ad);
            $ad->setOwner($this);
        }

        return $this;
    }

    public function removeAd(Pub $ad): self
    {
        if ($this->ads->removeElement($ad)) {
            // set the owning side to null (unless already changed)
            if ($ad->getOwner() === $this) {
                $ad->setOwner(null);
            }
        }

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
            $report->setReportingUser($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getReportingUser() === $this) {
                $report->setReportingUser(null);
            }
        }

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
            $stat->setFromUser($this);
        }

        return $this;
    }

    public function removeStat(Stat $stat): self
    {
        if ($this->stats->removeElement($stat)) {
            // set the owning side to null (unless already changed)
            if ($stat->getFromUser() === $this) {
                $stat->setFromUser(null);
            }
        }

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
            $share->setSharingBy($this);
        }

        return $this;
    }

    public function removeShare(Share $share): self
    {
        if ($this->shares->removeElement($share)) {
            // set the owning side to null (unless already changed)
            if ($share->getSharingBy() === $this) {
                $share->setSharingBy(null);
            }
        }

        return $this;
    }

    #[Groups(['read:user'])]
    public function getMessagesCount(): int
    {
        return count($this->messages);
    }

    #[Groups(['read:user'])]
    public function getFollowsCount(): int
    {
        return count($this->follows);
    }

    #[Groups(['read:user'])]
    public function getFollowersCount(): int
    {
        return count($this->followers);
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function setFollowed(bool $followed): void
    {
        $this->followed = $followed;
    }

    public function setUserToUserId(mixed $userToUserId): void
    {
        $this->userToUserId = $userToUserId;
    }

    public function getStripeCustomerId(): ?string
    {
        return $this->stripeCustomerId;
    }

    public function setStripeCustomerId(?string $stripeCustomerId): self
    {
        $this->stripeCustomerId = $stripeCustomerId;

        return $this;
    }
}
