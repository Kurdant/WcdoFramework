<?php

namespace App\Entity;

use App\Repository\FonctionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FonctionRepository::class)]
#[ORM\Table(name: 'fonction')]
#[ORM\UniqueConstraint(name: 'uniq_fonction_intitule', columns: ['intitule'])]
class Fonction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $intitule = null;

    /** @var Collection<int, Affectation> */
    #[ORM\OneToMany(mappedBy: 'fonction', targetEntity: Affectation::class)]
    private Collection $affectations;

    public function __construct()
    {
        $this->affectations = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getIntitule(): ?string { return $this->intitule; }
    public function setIntitule(string $intitule): static { $this->intitule = $intitule; return $this; }

    /** @return Collection<int, Affectation> */
    public function getAffectations(): Collection { return $this->affectations; }

    public function __toString(): string { return $this->intitule ?? ''; }
}
