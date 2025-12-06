<?php

namespace App\Entity;

use App\Entity\Trait\UuidPrimaryKey;
use App\Entity\Trait\SlugTrait;
use App\Repository\ResourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ResourceRepository::class)]
#[ORM\Table(name: 'resources')]
#[ORM\HasLifecycleCallbacks]
class Resource
{
    use UuidPrimaryKey;
    use SlugTrait;

    #[ORM\Column(length: 255)]
    #[Groups(['resource:read', 'resource:write', 'user:read'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['resource:read', 'resource:write'])]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: ResourceType::class, inversedBy: 'resources')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['resource:read'])]
    private ?ResourceType $type = null;

    #[ORM\ManyToOne(targetEntity: Subject::class, inversedBy: 'resources')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Groups(['resource:read'])]
    private ?Subject $subject = null;

    #[ORM\ManyToOne(targetEntity: Chapter::class, inversedBy: 'resources')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Groups(['resource:read'])]
    private ?Chapter $chapter = null;

    #[ORM\ManyToOne(targetEntity: Series::class, inversedBy: 'resources')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Groups(['resource:read'])]
    private ?Series $series = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['resource:read', 'resource:write'])]
    private ?int $year = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['resource:read', 'resource:write'])]
    private ?string $fileUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['resource:read', 'resource:write'])]
    private ?string $thumbnailUrl = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'resources')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['resource:read'])]
    private ?User $user = null;

    #[ORM\Column(length: 20)]
    #[Groups(['resource:read', 'resource:write'])]
    private string $status = 'draft';

    #[ORM\Column]
    #[Groups(['resource:read'])]
    private int $viewCount = 0;

    #[ORM\Column]
    #[Groups(['resource:read'])]
    private int $downloadCount = 0;

    #[ORM\Column(type: 'decimal', precision: 3, scale: 2, nullable: true)]
    #[Groups(['resource:read'])]
    private ?string $averageRating = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['resource:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['resource:read'])]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @var Collection<int, ResourceTag>
     */
    #[ORM\OneToMany(targetEntity: ResourceTag::class, mappedBy: 'resource', cascade: ['persist', 'remove'])]
    private Collection $resourceTags;

    /**
     * @var Collection<int, ResourceRating>
     */
    #[ORM\OneToMany(targetEntity: ResourceRating::class, mappedBy: 'resource', cascade: ['persist', 'remove'])]
    private Collection $ratings;

    #[ORM\OneToOne(targetEntity: ExamPaper::class, mappedBy: 'resource', cascade: ['persist', 'remove'])]
    private ?ExamPaper $examPaper = null;

    #[ORM\OneToOne(targetEntity: Solution::class, mappedBy: 'resource', cascade: ['persist', 'remove'])]
    private ?Solution $solution = null;

    #[ORM\OneToOne(targetEntity: RevisionSheet::class, mappedBy: 'resource', cascade: ['persist', 'remove'])]
    private ?RevisionSheet $revisionSheet = null;

    #[ORM\OneToOne(targetEntity: Exercise::class, mappedBy: 'resource', cascade: ['persist', 'remove'])]
    private ?Exercise $exercise = null;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->resourceTags = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTime();
    }

    public function setTitleWithSlug(string $title): static
    {
        $this->title = $title;
        $this->setSlug($title);
        return $this;
    }

    #[ORM\PreUpdate]
    public function updateTimestamp(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getType(): ?ResourceType
    {
        return $this->type;
    }

    public function setType(?ResourceType $type): static
    {
        $this->type = $type;
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

    public function getChapter(): ?Chapter
    {
        return $this->chapter;
    }

    public function setChapter(?Chapter $chapter): static
    {
        $this->chapter = $chapter;
        return $this;
    }

    public function getSeries(): ?Series
    {
        return $this->series;
    }

    public function setSeries(?Series $series): static
    {
        $this->series = $series;
        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): static
    {
        $this->year = $year;
        return $this;
    }

    public function getFileUrl(): ?string
    {
        return $this->fileUrl;
    }

    public function setFileUrl(?string $fileUrl): static
    {
        $this->fileUrl = $fileUrl;
        return $this;
    }

    public function getThumbnailUrl(): ?string
    {
        return $this->thumbnailUrl;
    }

    public function setThumbnailUrl(?string $thumbnailUrl): static
    {
        $this->thumbnailUrl = $thumbnailUrl;
        return $this;
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getViewCount(): int
    {
        return $this->viewCount;
    }

    public function setViewCount(int $viewCount): static
    {
        $this->viewCount = $viewCount;
        return $this;
    }

    public function getDownloadCount(): int
    {
        return $this->downloadCount;
    }

    public function setDownloadCount(int $downloadCount): static
    {
        $this->downloadCount = $downloadCount;
        return $this;
    }

    public function getAverageRating(): ?string
    {
        return $this->averageRating;
    }

    public function setAverageRating(?string $averageRating): static
    {
        $this->averageRating = $averageRating;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getResourceTags(): Collection
    {
        return $this->resourceTags;
    }

    public function addResourceTag(ResourceTag $resourceTag): static
    {
        if (!$this->resourceTags->contains($resourceTag)) {
            $this->resourceTags->add($resourceTag);
            $resourceTag->setResource($this);
        }
        return $this;
    }

    public function removeResourceTag(ResourceTag $resourceTag): static
    {
        if ($this->resourceTags->removeElement($resourceTag) && $resourceTag->getResource() === $this) {
            $resourceTag->setResource(null);
        }
        return $this;
    }

    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(ResourceRating $rating): static
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings->add($rating);
            $rating->setResource($this);
        }
        return $this;
    }

    public function removeRating(ResourceRating $rating): static
    {
        if ($this->ratings->removeElement($rating) && $rating->getResource() === $this) {
            $rating->setResource(null);
        }
        return $this;
    }

    public function getExamPaper(): ?ExamPaper
    {
        return $this->examPaper;
    }

    public function setExamPaper(?ExamPaper $examPaper): static
    {
        if ($examPaper === null && $this->examPaper !== null) {
            $this->examPaper->setResource(null);
        }
        if ($examPaper !== null && $examPaper->getResource() !== $this) {
            $examPaper->setResource($this);
        }
        $this->examPaper = $examPaper;
        return $this;
    }

    public function getSolution(): ?Solution
    {
        return $this->solution;
    }

    public function setSolution(?Solution $solution): static
    {
        if ($solution === null && $this->solution !== null) {
            $this->solution->setResource(null);
        }
        if ($solution !== null && $solution->getResource() !== $this) {
            $solution->setResource($this);
        }
        $this->solution = $solution;
        return $this;
    }

    public function getRevisionSheet(): ?RevisionSheet
    {
        return $this->revisionSheet;
    }

    public function setRevisionSheet(?RevisionSheet $revisionSheet): static
    {
        if ($revisionSheet === null && $this->revisionSheet !== null) {
            $this->revisionSheet->setResource(null);
        }
        if ($revisionSheet !== null && $revisionSheet->getResource() !== $this) {
            $revisionSheet->setResource($this);
        }
        $this->revisionSheet = $revisionSheet;
        return $this;
    }

    public function getExercise(): ?Exercise
    {
        return $this->exercise;
    }

    public function setExercise(?Exercise $exercise): static
    {
        if ($exercise === null && $this->exercise !== null) {
            $this->exercise->setResource(null);
        }
        if ($exercise !== null && $exercise->getResource() !== $this) {
            $exercise->setResource($this);
        }
        $this->exercise = $exercise;
        return $this;
    }
}
