<?php

namespace App\Entity;

use App\Entity\Trait\UuidPrimaryKey;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\Table(name: 'tags')]
class Tag
{
    use UuidPrimaryKey;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $name = null;

    /**
     * @var Collection<int, ResourceTag>
     */
    #[ORM\OneToMany(targetEntity: ResourceTag::class, mappedBy: 'tag', cascade: ['persist', 'remove'])]
    private Collection $resourceTags;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->resourceTags = new ArrayCollection();
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

    public function getResourceTags(): Collection
    {
        return $this->resourceTags;
    }

    public function addResourceTag(ResourceTag $resourceTag): static
    {
        if (!$this->resourceTags->contains($resourceTag)) {
            $this->resourceTags->add($resourceTag);
            $resourceTag->setTag($this);
        }
        return $this;
    }

    public function removeResourceTag(ResourceTag $resourceTag): static
    {
        if ($this->resourceTags->removeElement($resourceTag) && $resourceTag->getTag() === $this) {
            $resourceTag->setTag(null);
        }
        return $this;
    }
}
