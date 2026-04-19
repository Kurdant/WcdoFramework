<?php

namespace App\Repository;

use App\Entity\Collaborateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Collaborateur>
 */
class CollaborateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Collaborateur::class);
    }

    /**
     * @return list<Collaborateur>
     */
    public function findByFilters(?string $nom, ?string $prenom, ?string $email): array
    {
        $qb = $this->createQueryBuilder('c');

        if ($nom !== null && $nom !== '') {
            $qb->andWhere('c.nom LIKE :nom')->setParameter('nom', '%' . $nom . '%');
        }
        if ($prenom !== null && $prenom !== '') {
            $qb->andWhere('c.prenom LIKE :prenom')->setParameter('prenom', '%' . $prenom . '%');
        }
        if ($email !== null && $email !== '') {
            $qb->andWhere('c.email LIKE :email')->setParameter('email', '%' . $email . '%');
        }

        return $qb->orderBy('c.nom', 'ASC')->addOrderBy('c.prenom', 'ASC')->getQuery()->getResult();
    }

    /**
     * Collaborateurs qui n'ont AUCUNE affectation active (dateFin IS NULL).
     *
     * @return list<Collaborateur>
     */
    public function findNonAffectes(): array
    {
        // Sub-query pour collabs avec affectation active
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.affectations', 'a', 'WITH', 'a.dateFin IS NULL')
            ->andWhere('a.id IS NULL')
            ->orderBy('c.nom', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findOneByEmail(string $email): ?Collaborateur
    {
        return $this->findOneBy(['email' => $email]);
    }
}
