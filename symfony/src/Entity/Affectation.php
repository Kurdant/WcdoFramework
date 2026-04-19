<?php

namespace App\Entity;

use App\Repository\AffectationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AffectationRepository::class)]
#[ORM\Table(name: 'affectation')]
#[ORM\Index(name: 'idx_affectation_date_fin', columns: ['date_fin'])]
class Affectation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotNull]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\ManyToOne(inversedBy: 'affectations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Collaborateur $collaborateur = null;

    #[ORM\ManyToOne(inversedBy: 'affectations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Restaurant $restaurant = null;

    #[ORM\ManyToOne(inversedBy: 'affectations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Fonction $fonction = null;

    public function getId(): ?int { return $this->id; }

    public function getDateDebut(): ?\DateTimeInterface { return $this->dateDebut; }
    public function setDateDebut(\DateTimeInterface $dateDebut): static { $this->dateDebut = $dateDebut; return $this; }

    public function getDateFin(): ?\DateTimeInterface { return $this->dateFin; }
    public function setDateFin(?\DateTimeInterface $dateFin): static { $this->dateFin = $dateFin; return $this; }

    public function getCollaborateur(): ?Collaborateur { return $this->collaborateur; }
    public function setCollaborateur(?Collaborateur $c): static { $this->collaborateur = $c; return $this; }

    public function getRestaurant(): ?Restaurant { return $this->restaurant; }
    public function setRestaurant(?Restaurant $r): static { $this->restaurant = $r; return $this; }

    public function getFonction(): ?Fonction { return $this->fonction; }
    public function setFonction(?Fonction $f): static { $this->fonction = $f; return $this; }

    public function isActive(): bool { return $this->dateFin === null; }
}
