<?php

namespace App\Entity;

use App\Entity\Trait\UuidPrimaryKey;
use App\Repository\PracticeSessionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PracticeSessionRepository::class)]
#[ORM\Table(name: 'practice_sessions')]
class PracticeSession
{
    use UuidPrimaryKey;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'practiceSessions')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['practice_session:read', 'user:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Subject::class, inversedBy: 'practiceSessions')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['practice_session:read', 'user:read'])]
    private ?Subject $subject = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['practice_session:read', 'user:read'])]
    private ?\DateTimeImmutable $startTime = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['practice_session:read', 'user:read'])]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['practice_session:read', 'user:read'])]
    private ?int $durationMinutes = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['practice_session:read'])]
    private ?array $resourcesUsed = null;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->startTime = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): static
    {
        $this->subject = $subject;
        return $this;
    }

    public function getStartTime(): ?\DateTimeImmutable
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeImmutable $startTime): static
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeInterface $endTime): static
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function getDurationMinutes(): ?int
    {
        return $this->durationMinutes;
    }

    public function setDurationMinutes(?int $durationMinutes): static
    {
        $this->durationMinutes = $durationMinutes;
        return $this;
    }

    public function getResourcesUsed(): ?array
    {
        return $this->resourcesUsed;
    }

    public function setResourcesUsed(?array $resourcesUsed): static
    {
        $this->resourcesUsed = $resourcesUsed;
        return $this;
    }
}
