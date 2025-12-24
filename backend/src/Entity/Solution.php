<?php

namespace App\Entity;

use App\Entity\Trait\UuidPrimaryKey;
use App\Repository\SolutionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SolutionRepository::class)]
#[ORM\Table(name: 'solutions')]
class Solution
{
    use UuidPrimaryKey;

    #[ORM\OneToOne(targetEntity: Resource::class, inversedBy: 'solution')]
    #[ORM\JoinColumn(nullable: false, unique: true, onDelete: 'CASCADE')]
    #[Groups(['solution:read', 'resource:read'])]
    private ?Resource $resource = null;

    #[ORM\ManyToOne(targetEntity: ExamPaper::class, inversedBy: 'solutions')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Groups(['solution:read'])]
    private ?ExamPaper $examPaper = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['solution:read'])]
    private ?string $contentText = null;

    public function __construct()
    {
        $this->id = Uuid::v7();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getResource(): ?Resource
    {
        return $this->resource;
    }

    public function setResource(?Resource $resource): static
    {
        $this->resource = $resource;
        return $this;
    }

    public function getExamPaper(): ?ExamPaper
    {
        return $this->examPaper;
    }

    public function setExamPaper(?ExamPaper $examPaper): static
    {
        $this->examPaper = $examPaper;
        return $this;
    }

    public function getContentText(): ?string
    {
        return $this->contentText;
    }

    public function setContentText(?string $contentText): static
    {
        $this->contentText = $contentText;
        return $this;
    }
}
