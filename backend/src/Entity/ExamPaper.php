<?php

namespace App\Entity;

use App\Entity\Trait\UuidPrimaryKey;
use App\Repository\ExamPaperRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ExamPaperRepository::class)]
#[ORM\Table(name: 'exam_papers')]
class ExamPaper
{
    use UuidPrimaryKey;

    #[ORM\OneToOne(targetEntity: Resource::class, inversedBy: 'examPaper')]
    #[ORM\JoinColumn(nullable: false, unique: true, onDelete: 'CASCADE')]
    private ?Resource $resource = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $examType = null;

    #[ORM\Column(nullable: true)]
    private ?int $durationMinutes = null;

    /**
     * @var Collection<int, Solution>
     */
    #[ORM\OneToMany(targetEntity: Solution::class, mappedBy: 'examPaper')]
    private Collection $solutions;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->solutions = new ArrayCollection();
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

    public function getExamType(): ?string
    {
        return $this->examType;
    }

    public function setExamType(?string $examType): static
    {
        $this->examType = $examType;
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

    public function getSolutions(): Collection
    {
        return $this->solutions;
    }

    public function addSolution(Solution $solution): static
    {
        if (!$this->solutions->contains($solution)) {
            $this->solutions->add($solution);
            $solution->setExamPaper($this);
        }
        return $this;
    }

    public function removeSolution(Solution $solution): static
    {
        if ($this->solutions->removeElement($solution) && $solution->getExamPaper() === $this) {
            $solution->setExamPaper(null);
        }
        return $this;
    }
}
