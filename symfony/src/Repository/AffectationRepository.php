<?php

namespace App\Repository;

use App\Entity\Affectation;
use App\Entity\Collaborateur;
use App\Entity\Fonction;
use App\Entity\Restaurant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Affectation>
 */
class AffectationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Affectation::class);
    }

    /**
     * @return list<Affectation>
     */
    public function findByFilters(
        ?Fonction $fonction,
        ?\DateTimeInterface $dateDebut,
        ?\DateTimeInterface $dateFin,
        ?string $ville,
    ): array {
        $qb = $this->createQueryBuilder('a')
            ->innerJoin('a.restaurant', 'r')->addSelect('r')
            ->innerJoin('a.fonction', 'f')->addSelect('f')
            ->innerJoin('a.collaborateur', 'c')->addSelect('c');

        if ($fonction !== null) {
            $qb->andWhere('a.fonction = :f')->setParameter('f', $fonction);
        }
        if ($dateDebut !== null) {
            $qb->andWhere('a.dateDebut >= :dd')->setParameter('dd', $dateDebut);
        }
        if ($dateFin !== null) {
            $qb->andWhere('(a.dateFin IS NULL OR a.dateFin <= :df)')->setParameter('df', $dateFin);
        }
        if ($ville !== null && $ville !== '') {
            $qb->andWhere('r.ville LIKE :v')->setParameter('v', '%' . $ville . '%');
        }

        return $qb->orderBy('a.dateDebut', 'DESC')->getQuery()->getResult();
    }

    /**
     * @return list<Affectation>
     */
    public function findActivesByRestaurant(
        Restaurant $restaurant,
        ?Fonction $fonction,
        ?string $nom,
        ?\DateTimeInterface $dateDebut,
    ): array {
        $qb = $this->createQueryBuilder('a')
            ->innerJoin('a.collaborateur', 'c')->addSelect('c')
            ->innerJoin('a.fonction', 'f')->addSelect('f')
            ->andWhere('a.restaurant = :r')->setParameter('r', $restaurant)
            ->andWhere('a.dateFin IS NULL');

        if ($fonction !== null) {
            $qb->andWhere('a.fonction = :f')->setParameter('f', $fonction);
        }
        if ($nom !== null && $nom !== '') {
            $qb->andWhere('c.nom LIKE :nom')->setParameter('nom', '%' . $nom . '%');
        }
        if ($dateDebut !== null) {
            $qb->andWhere('a.dateDebut >= :dd')->setParameter('dd', $dateDebut);
        }

        return $qb->orderBy('c.nom', 'ASC')->getQuery()->getResult();
    }

    /**
     * @return list<Affectation>
     */
    public function findAllByRestaurant(
        Restaurant $restaurant,
        ?Fonction $fonction,
        ?string $nom,
        ?\DateTimeInterface $dateDebut,
    ): array {
        $qb = $this->createQueryBuilder('a')
            ->innerJoin('a.collaborateur', 'c')->addSelect('c')
            ->innerJoin('a.fonction', 'f')->addSelect('f')
            ->andWhere('a.restaurant = :r')->setParameter('r', $restaurant);

        if ($fonction !== null) {
            $qb->andWhere('a.fonction = :f')->setParameter('f', $fonction);
        }
        if ($nom !== null && $nom !== '') {
            $qb->andWhere('c.nom LIKE :nom')->setParameter('nom', '%' . $nom . '%');
        }
        if ($dateDebut !== null) {
            $qb->andWhere('a.dateDebut >= :dd')->setParameter('dd', $dateDebut);
        }

        return $qb->orderBy('a.dateDebut', 'DESC')->getQuery()->getResult();
    }

    /**
     * @return list<Affectation>
     */
    public function findActivesByCollaborateur(Collaborateur $collaborateur): array
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.restaurant', 'r')->addSelect('r')
            ->innerJoin('a.fonction', 'f')->addSelect('f')
            ->andWhere('a.collaborateur = :c')->setParameter('c', $collaborateur)
            ->andWhere('a.dateFin IS NULL')
            ->orderBy('a.dateDebut', 'DESC')
            ->getQuery()->getResult();
    }

    /**
     * @return list<Affectation>
     */
    public function findAllByCollaborateur(
        Collaborateur $collaborateur,
        ?Fonction $fonction,
        ?\DateTimeInterface $dateDebut,
    ): array {
        $qb = $this->createQueryBuilder('a')
            ->innerJoin('a.restaurant', 'r')->addSelect('r')
            ->innerJoin('a.fonction', 'f')->addSelect('f')
            ->andWhere('a.collaborateur = :c')->setParameter('c', $collaborateur);

        if ($fonction !== null) {
            $qb->andWhere('a.fonction = :f')->setParameter('f', $fonction);
        }
        if ($dateDebut !== null) {
            $qb->andWhere('a.dateDebut >= :dd')->setParameter('dd', $dateDebut);
        }

        return $qb->orderBy('a.dateDebut', 'DESC')->getQuery()->getResult();
    }
}
