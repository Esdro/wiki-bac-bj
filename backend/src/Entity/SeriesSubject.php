<?php

namespace App\Entity;

use App\Entity\Trait\UuidPrimaryKey;
use App\Repository\SeriesSubjectRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SeriesSubjectRepository::class)]
#[ORM\Table(name: 'series_subjects')]
#[ORM\UniqueConstraint(name: 'unique_series_subject', columns: ['series_id', 'subject_id'])]
class SeriesSubject
{
    use UuidPrimaryKey;

    #[ORM\ManyToOne(targetEntity: Series::class, inversedBy: 'seriesSubjects')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Series $series = null;

    #[ORM\ManyToOne(targetEntity: Subject::class, inversedBy: 'seriesSubjects')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Subject $subject = null;

    #[ORM\Column(type: 'decimal', precision: 3, scale: 1, nullable: true)]
    private ?string $coefficient = null;

    public function __construct()
    {
        $this->id = Uuid::v7();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
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

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): static
    {
        $this->subject = $subject;
        return $this;
    }

    public function getCoefficient(): ?string
    {
        return $this->coefficient;
    }

    public function setCoefficient(?string $coefficient): static
    {
        $this->coefficient = $coefficient;
        return $this;
    }
}
