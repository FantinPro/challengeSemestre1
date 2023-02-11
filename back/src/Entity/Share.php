<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\CancelShareController;
use App\Controller\DeleteAllReportsFromMessageController;
use App\Repository\ShareRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ShareRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            securityPostDenormalize: "is_granted('ROLE_USER') and object.getSharingBy() == user",
        ),
        new Get(
            uriTemplate: '/shares/delete',
            controller: CancelShareController::class,
            security: "is_granted('ROLE_USER')",
            read: false,
        )
    ],
    normalizationContext: ['groups' => ['read:share']],
    denormalizationContext: ['groups' => ['write:share']],
)]
#[UniqueEntity(
    fields: ['sharingBy', 'sharedMessage'],
    message: 'You already shared it.',
    errorPath: 'sharedMessage',
)]
class Share
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:share', 'write:share'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'shares')]
    #[Groups(['read:share', 'write:share'])]
    private ?User $sharingBy = null;

    #[ORM\ManyToOne(inversedBy: 'shares')]
    #[Groups(['read:share', 'write:share'])]
    private ?Message $sharedMessage = null;

    #[Timestampable(on: 'create')]
    #[ORM\Column(name: 'created', type: Types::DATETIME_MUTABLE)]
    private $created;

    public function __construct()
    {
        $this->created = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSharingBy(): ?User
    {
        return $this->sharingBy;
    }

    public function setSharingBy(?User $sharingBy): self
    {
        $this->sharingBy = $sharingBy;

        return $this;
    }

    public function getSharedMessage(): ?Message
    {
        return $this->sharedMessage;
    }

    public function setSharedMessage(?Message $sharedMessage): self
    {
        $this->sharedMessage = $sharedMessage;

        return $this;
    }
}
