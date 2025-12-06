<?php

namespace App\Entity;

use App\Entity\Trait\UuidPrimaryKey;
use App\Entity\Trait\SlugTrait;
use App\Repository\ChapterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ChapterRepository::class)]
#[ORM\Table(name: 'chapters')]
#[ORM\HasLifecycleCallbacks]
class Chapter
{
    use UuidPrimaryKey;
    use SlugTrait;

    #[ORM\ManyToOne(targetEntity: Subject::class, inversedBy: 'chapters')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['chapter:read', 'resource:read'])]
    private ?Subject $subject = null;

    #[ORM\Column(length: 255)]
    #[Groups(['chapter:read', 'resource:read'])]
    private ?string $title = null;

    #[ORM\Column]
    #[Groups(['chapter:read'])]
    private ?int $orderNum = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['chapter:read'])]
    private ?string $description = null;

    /**
     * @var Collection<int, Resource>
     */
    #[ORM\OneToMany(targetEntity: Resource::class, mappedBy: 'chapter')]
    private Collection $resources;

    /**
     * @var Collection<int, UserProgress>
     */
    #[ORM\OneToMany(targetEntity: UserProgress::class, mappedBy: 'chapter', cascade: ['persist', 'remove'])]
    private Collection $progressEntries;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->resources = new ArrayCollection();
        $this->progressEntries = new ArrayCollection();
    }

    public function setTitleWithSlug(string $title): static
    {
        $this->title = $title;
        $this->setSlug($title);
        return $this;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getOrderNum(): ?int
    {
        return $this->orderNum;
    }

    public function setOrderNum(int $orderNum): static
    {
        $this->orderNum = $orderNum;
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

    public function getResources(): Collection
    {
        return $this->resources;
    }

    public function addResource(Resource $resource): static
    {
        if (!$this->resources->contains($resource)) {
            $this->resources->add($resource);
            $resource->setChapter($this);
        }
        return $this;
    }

    public function removeResource(Resource $resource): static
    {
        if ($this->resources->removeElement($resource) && $resource->getChapter() === $this) {
            $resource->setChapter(null);
        }
        return $this;
    }

    public function getProgressEntries(): Collection
    {
        return $this->progressEntries;
    }

    public function addProgressEntry(UserProgress $progressEntry): static
    {
        if (!$this->progressEntries->contains($progressEntry)) {
            $this->progressEntries->add($progressEntry);
            $progressEntry->setChapter($this);
        }
        return $this;
    }

    public function removeProgressEntry(UserProgress $progressEntry): static
    {
        if ($this->progressEntries->removeElement($progressEntry) && $progressEntry->getChapter() === $this) {
            $progressEntry->setChapter(null);
        }
        return $this;
    }
}
