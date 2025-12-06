<?php

namespace App\Entity;

use App\Entity\Trait\UuidPrimaryKey;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\UniqueConstraint(name: 'UNIQ_email', columns: ['email'])]
#[ORM\UniqueConstraint(name: 'UNIQ_username', columns: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use UuidPrimaryKey;

    #[ORM\Column(length: 100, unique: true)]
    #[Groups(['user:read', 'user:write', 'resource:read', 'forum:read', 'rating:read'])]
    private ?string $email = null;

    #[ORM\Column(name: 'password_hash', length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Groups(['user:read', 'user:write', 'resource:read', 'forum:read', 'rating:read'])]
    private ?string $username = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['user:read', 'user:write', 'resource:read', 'forum:read'])]
    private ?string $fullName = null;

    #[ORM\ManyToOne(targetEntity: Role::class, inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'role_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[Groups(['user:read'])]
    private ?Role $role = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $avatarUrl = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $bio = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['user:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['user:read'])]
    private ?\DateTimeInterface $lastLogin = null;

    #[ORM\Column(length: 20)]
    #[Groups(['user:read', 'user:write'])]
    private string $status = 'active';

    /**
     * @var Collection<int, Resource>
     */
    #[ORM\OneToMany(targetEntity: Resource::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    #[Groups(['user:read'])]
    private Collection $resources;

    /**
     * @var Collection<int, ResourceRating>
     */
    #[ORM\OneToMany(targetEntity: ResourceRating::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private Collection $ratings;

    /**
     * @var Collection<int, UserProgress>
     */
    #[ORM\OneToMany(targetEntity: UserProgress::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private Collection $progressEntries;

    /**
     * @var Collection<int, PracticeSession>
     */
    #[ORM\OneToMany(targetEntity: PracticeSession::class, mappedBy: 'user')]
    private Collection $practiceSessions;

    /**
     * @var Collection<int, ForumTopic>
     */
    #[ORM\OneToMany(targetEntity: ForumTopic::class, mappedBy: 'user')]
    private Collection $forumTopics;

    /**
     * @var Collection<int, ForumPost>
     */
    #[ORM\OneToMany(targetEntity: ForumPost::class, mappedBy: 'user')]
    private Collection $forumPosts;

    public function __construct()
    {
        // Génération UUID v7 (défini dans le trait)
        $this->id = Uuid::v7();
        $this->resources = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->progressEntries = new ArrayCollection();
        $this->practiceSessions = new ArrayCollection();
        $this->forumTopics = new ArrayCollection();
        $this->forumPosts = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    // ========================================
    // UserInterface implementation
    // ========================================

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        // Retourne toujours au moins ROLE_USER
        $roles = ['ROLE_USER'];
        
        // Ajoute le rôle depuis la relation si défini
        if ($this->role && $this->role->getName()) {
            $roles[] = 'ROLE_' . strtoupper($this->role->getName());
        }
        
        return array_unique($roles);
    }

    public function eraseCredentials(): void
    {
        // Nettoie les données sensibles temporaires si nécessaire
        // (par exemple un plainPassword si tu l'utilises)
    }

    // ========================================
    // PasswordAuthenticatedUserInterface implementation
    // ========================================

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    // ========================================
    // Getters / Setters standard
    // ========================================

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }



    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): static
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl): static
    {
        $this->avatarUrl = $avatarUrl;
        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;
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

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $lastLogin): static
    {
        $this->lastLogin = $lastLogin;
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

    public function getResources(): Collection
    {
        return $this->resources;
    }

    public function addResource(Resource $resource): static
    {
        if (!$this->resources->contains($resource)) {
            $this->resources->add($resource);
            $resource->setUser($this);
        }
        return $this;
    }

    public function removeResource(Resource $resource): static
    {
        if ($this->resources->removeElement($resource) && $resource->getUser() === $this) {
            $resource->setUser(null);
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
            $rating->setUser($this);
        }
        return $this;
    }

    public function removeRating(ResourceRating $rating): static
    {
        if ($this->ratings->removeElement($rating) && $rating->getUser() === $this) {
            $rating->setUser(null);
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
            $progressEntry->setUser($this);
        }
        return $this;
    }

    public function removeProgressEntry(UserProgress $progressEntry): static
    {
        if ($this->progressEntries->removeElement($progressEntry) && $progressEntry->getUser() === $this) {
            $progressEntry->setUser(null);
        }
        return $this;
    }

    public function getPracticeSessions(): Collection
    {
        return $this->practiceSessions;
    }

    public function addPracticeSession(PracticeSession $practiceSession): static
    {
        if (!$this->practiceSessions->contains($practiceSession)) {
            $this->practiceSessions->add($practiceSession);
            $practiceSession->setUser($this);
        }
        return $this;
    }

    public function removePracticeSession(PracticeSession $practiceSession): static
    {
        if ($this->practiceSessions->removeElement($practiceSession) && $practiceSession->getUser() === $this) {
            $practiceSession->setUser(null);
        }
        return $this;
    }

    public function getForumTopics(): Collection
    {
        return $this->forumTopics;
    }

    public function addForumTopic(ForumTopic $forumTopic): static
    {
        if (!$this->forumTopics->contains($forumTopic)) {
            $this->forumTopics->add($forumTopic);
            $forumTopic->setUser($this);
        }
        return $this;
    }

    public function removeForumTopic(ForumTopic $forumTopic): static
    {
        if ($this->forumTopics->removeElement($forumTopic) && $forumTopic->getUser() === $this) {
            $forumTopic->setUser(null);
        }
        return $this;
    }

    public function getForumPosts(): Collection
    {
        return $this->forumPosts;
    }

    public function addForumPost(ForumPost $forumPost): static
    {
        if (!$this->forumPosts->contains($forumPost)) {
            $this->forumPosts->add($forumPost);
            $forumPost->setUser($this);
        }
        return $this;
    }

    public function removeForumPost(ForumPost $forumPost): static
    {
        if ($this->forumPosts->removeElement($forumPost) && $forumPost->getUser() === $this) {
            $forumPost->setUser(null);
        }
        return $this;
    }
}
