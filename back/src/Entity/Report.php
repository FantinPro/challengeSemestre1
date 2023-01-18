<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ReportRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Choice;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('ROLE_MODERATOR')",
        ),
        new Post(
            security: "is_granted('ROLE_USER')",
        ),
        new GetCollection(
            security: "is_granted('ROLE_MODERATOR')",
        )
    ]
)]
#[UniqueEntity(
    fields: ['reportingUser', 'reportedMessage'],
    message: 'You already reported it.',
    errorPath: 'reportedMessage',
)]
class Report
{

    const REPORT_TYPES = ['spam', 'abusive', 'racism', 'harassment', 'other'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $reportingUser = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Message $reportedMessage = null;

    #[ORM\Column(length: 30)]
    #[Choice(choices: self::REPORT_TYPES)]
    private ?string $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReportingUser(): ?User
    {
        return $this->reportingUser;
    }

    public function setReportingUser(?User $reportingUser): self
    {
        $this->reportingUser = $reportingUser;

        return $this;
    }

    public function getReportedMessage(): ?Message
    {
        return $this->reportedMessage;
    }

    public function setReportedMessage(?Message $reportedMessage): self
    {
        $this->reportedMessage = $reportedMessage;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
