<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
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

    #[ORM\Column(length: 50)]
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
