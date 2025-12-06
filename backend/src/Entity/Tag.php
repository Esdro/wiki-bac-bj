<?php

namespace App\Entity;

use App\Entity\Trait\UuidPrimaryKey;
use App\Entity\Trait\SlugTrait;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\Table(name: 'tags')]
#[ORM\HasLifecycleCallbacks]
class Tag
{
    use UuidPrimaryKey;
    use SlugTrait;

    #[ORM\Column(length: 50, unique: true)]
    #[Groups(['tag:read', 'resource:read'])]
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
