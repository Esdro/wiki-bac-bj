<?php

namespace App\Entity;

use App\Entity\Trait\UuidPrimaryKey;
use App\Repository\ResourceTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ResourceTypeRepository::class)]
#[ORM\Table(name: 'resource_types')]
class ResourceType
{
    use UuidPrimaryKey;

    #[ORM\Column(length: 50, unique: true)]
    #[Groups(['resource_type:read', 'resource:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['resource_type:read'])]
    private ?string $description = null;

    /**
     * @var Collection<int, Resource>
     */
    #[ORM\OneToMany(targetEntity: Resource::class, mappedBy: 'type', cascade: ['persist', 'remove'])]
    private Collection $resources;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->resources = new ArrayCollection();
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
            $resource->setType($this);
        }
        return $this;
    }

    public function removeResource(Resource $resource): static
    {
        if ($this->resources->removeElement($resource) && $resource->getType() === $this) {
            $resource->setType(null);
        }
        return $this;
    }
}
