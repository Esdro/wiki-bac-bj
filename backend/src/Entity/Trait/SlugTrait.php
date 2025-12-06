<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait SlugTrait
{
    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $this->generateSlug($slug ?? '');
        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function generateSlugFromName(): void
    {
        if (method_exists($this, 'getName')) {
            $this->slug = $this->generateSlug($this->getName());
        } elseif (method_exists($this, 'getTitle')) {
            $this->slug = $this->generateSlug($this->getTitle());
        }
    }

    public function generateSlug(string $text): string
    {
        // Convertir en minuscules
        $slug = mb_strtolower($text, 'UTF-8');

        // Remplacer les caractères accentués
        $slug = strtr($slug, [
            'à' => 'a',
            'â' => 'a',
            'ä' => 'a',
            'é' => 'e',
            'è' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'î' => 'i',
            'ï' => 'i',
            'ô' => 'o',
            'ö' => 'o',
            'û' => 'u',
            'ü' => 'u',
            'ç' => 'c',
            'œ' => 'oe',
            'æ' => 'ae',
        ]);

        // Remplacer les espaces et caractères spéciaux par des tirets
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

        // Supprimer les tirets au début et à la fin
        $slug = trim($slug, '-');

        // Limiter à 255 caractères
        return substr($slug, 0, 255);
    }
}
