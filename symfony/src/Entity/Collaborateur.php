<?php

namespace App\Entity;

use App\Repository\CollaborateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CollaborateurRepository::class)]
#[ORM\Table(name: 'collaborateur')]
#[ORM\UniqueConstraint(name: 'uniq_collaborateur_email', columns: ['email'])]
class Collaborateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $prenom = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotNull]
    private ?\DateTimeInterface $dateEmbauche = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $administrateur = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motDePasse = null;

    /** @var list<string> */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /** @var Collection<int, Affectation> */
    #[ORM\OneToMany(mappedBy: 'collaborateur', targetEntity: Affectation::class)]
    private Collection $affectations;

    public function __construct()
    {
        $this->affectations = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(string $prenom): static { $this->prenom = $prenom; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getDateEmbauche(): ?\DateTimeInterface { return $this->dateEmbauche; }
    public function setDateEmbauche(\DateTimeInterface $dateEmbauche): static { $this->dateEmbauche = $dateEmbauche; return $this; }

    public function isAdministrateur(): bool { return $this->administrateur; }
    public function setAdministrateur(bool $administrateur): static
    {
        $this->administrateur = $administrateur;
        $this->roles = $administrateur ? ['ROLE_ADMIN'] : ['ROLE_USER'];
        return $this;
    }

    public function getMotDePasse(): ?string { return $this->motDePasse; }
    public function setMotDePasse(?string $motDePasse): static { $this->motDePasse = $motDePasse; return $this; }

    /** @return Collection<int, Affectation> */
    public function getAffectations(): Collection { return $this->affectations; }

    public function getUserIdentifier(): string { return (string) $this->email; }

    /** @return list<string> */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_values(array_unique($roles));
    }

    /** @param list<string> $roles */
    public function setRoles(array $roles): static { $this->roles = $roles; return $this; }

    public function getPassword(): ?string { return $this->motDePasse; }

    public function eraseCredentials(): void {}

    public function __toString(): string
    {
        return trim(sprintf('%s %s', $this->prenom ?? '', $this->nom ?? ''));
    }
}
