<?php

namespace App\Entity;

use App\Repository\ShareRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;

#[ORM\Entity(repositoryClass: ShareRepository::class)]
class Share
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'shares')]
    private ?User $sharingBy = null;

    #[ORM\ManyToOne(inversedBy: 'shares', cascade: ['remove'])]
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
