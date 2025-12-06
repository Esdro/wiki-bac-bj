<?php

namespace App\Entity;

use App\Entity\Trait\UuidPrimaryKey;
use App\Entity\Trait\SlugTrait;
use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
#[ORM\Table(name: 'subjects')]
#[ORM\HasLifecycleCallbacks]
class Subject
{
    use UuidPrimaryKey;
    use SlugTrait;

    #[ORM\Column(length: 100)]
    #[Groups(['subject:read', 'resource:read', 'chapter:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 10, unique: true)]
    #[Groups(['subject:read'])]
    private ?string $code = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['subject:read'])]
    private ?string $icon = null;

    /**
     * @var Collection<int, Chapter>
     */
    #[ORM\OneToMany(targetEntity: Chapter::class, mappedBy: 'subject', cascade: ['persist', 'remove'])]
    private Collection $chapters;

    /**
     * @var Collection<int, SeriesSubject>
     */
    #[ORM\OneToMany(targetEntity: SeriesSubject::class, mappedBy: 'subject', cascade: ['persist', 'remove'])]
    private Collection $seriesSubjects;

    /**
     * @var Collection<int, Resource>
     */
    #[ORM\OneToMany(targetEntity: Resource::class, mappedBy: 'subject')]
    private Collection $resources;

    /**
     * @var Collection<int, PracticeSession>
     */
    #[ORM\OneToMany(targetEntity: PracticeSession::class, mappedBy: 'subject')]
    private Collection $practiceSessions;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->chapters = new ArrayCollection();
        $this->seriesSubjects = new ArrayCollection();
        $this->resources = new ArrayCollection();
        $this->practiceSessions = new ArrayCollection();
    }

    public function setNameWithSlug(string $name): static
    {
        $this->name = $name;
        $this->setSlug($name);
        return $this;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;
        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): static
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return Collection<int, Chapter>
     */
    public function getChapters(): Collection
    {
        return $this->chapters;
    }

    public function addChapter(Chapter $chapter): static
    {
        if (!$this->chapters->contains($chapter)) {
            $this->chapters->add($chapter);
            $chapter->setSubject($this);
        }
        return $this;
    }

    public function removeChapter(Chapter $chapter): static
    {
        if ($this->chapters->removeElement($chapter)) {
            if ($chapter->getSubject() === $this) {
                $chapter->setSubject(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, SeriesSubject>
     */
    public function getSeriesSubjects(): Collection
    {
        return $this->seriesSubjects;
    }

    public function addSeriesSubject(SeriesSubject $seriesSubject): static
    {
        if (!$this->seriesSubjects->contains($seriesSubject)) {
            $this->seriesSubjects->add($seriesSubject);
            $seriesSubject->setSubject($this);
        }
        return $this;
    }

    public function removeSeriesSubject(SeriesSubject $seriesSubject): static
    {
        if ($this->seriesSubjects->removeElement($seriesSubject)) {
            if ($seriesSubject->getSubject() === $this) {
                $seriesSubject->setSubject(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Resource>
     */
    public function getResources(): Collection
    {
        return $this->resources;
    }

    public function addResource(Resource $resource): static
    {
        if (!$this->resources->contains($resource)) {
            $this->resources->add($resource);
            $resource->setSubject($this);
        }
        return $this;
    }

    public function removeResource(Resource $resource): static
    {
        if ($this->resources->removeElement($resource)) {
            if ($resource->getSubject() === $this) {
                $resource->setSubject(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, PracticeSession>
     */
    public function getPracticeSessions(): Collection
    {
        return $this->practiceSessions;
    }

    public function addPracticeSession(PracticeSession $practiceSession): static
    {
        if (!$this->practiceSessions->contains($practiceSession)) {
            $this->practiceSessions->add($practiceSession);
            $practiceSession->setSubject($this);
        }
        return $this;
    }

    public function removePracticeSession(PracticeSession $practiceSession): static
    {
        if ($this->practiceSessions->removeElement($practiceSession)) {
            if ($practiceSession->getSubject() === $this) {
                $practiceSession->setSubject(null);
            }
        }
        return $this;
    }
}
