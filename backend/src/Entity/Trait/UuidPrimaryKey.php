<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Attribute\Groups;

trait UuidPrimaryKey
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['chapter:read', 'subject:read', 'tag:read', 'resource:read', 'user:read', 'role:read', 'practice_session:read', 'series:read', 'series_subject:read', 'resource_tag:read', 'resource_subject:read', 'resource_series:read', 'revision_sheet:read', 'solution:read'])]
    private ?Uuid $id = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }
}
