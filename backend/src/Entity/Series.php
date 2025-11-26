<?php

namespace App\Entity;

use App\Entity\Trait\UuidPrimaryKey;
use App\Repository\SeriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SeriesRepository::class)]
#[ORM\Table(name: 'series')]
class Series
{
    use UuidPrimaryKey;

    #[ORM\Column(length: 10, unique: true)]
    private ?string $code = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, SeriesSubject>
     */
    #[ORM\OneToMany(targetEntity: SeriesSubject::class, mappedBy: 'series', cascade: ['persist', 'remove'])]
    private Collection $seriesSubjects;

    /**
     * @var Collection<int, Resource>
     */
    #[ORM\OneToMany(targetEntity: Resource::class, mappedBy: 'series')]
    private Collection $resources;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->seriesSubjects = new ArrayCollection();
        $this->resources = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
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
            $seriesSubject->setSeries($this);
        }
        return $this;
    }

    public function removeSeriesSubject(SeriesSubject $seriesSubject): static
    {
        if ($this->seriesSubjects->removeElement($seriesSubject)) {
            if ($seriesSubject->getSeries() === $this) {
                $seriesSubject->setSeries(null);
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
            $resource->setSeries($this);
        }
        return $this;
    }

    public function removeResource(Resource $resource): static
    {
        if ($this->resources->removeElement($resource)) {
            if ($resource->getSeries() === $this) {
                $resource->setSeries(null);
            }
        }
        return $this;
    }
}
