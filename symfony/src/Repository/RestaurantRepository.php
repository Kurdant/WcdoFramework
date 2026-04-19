<?php

namespace App\Repository;

use App\Entity\Restaurant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Restaurant>
 */
class RestaurantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Restaurant::class);
    }

    /**
     * @return list<Restaurant>
     */
    public function findByFilters(?string $nom, ?string $codePostal, ?string $ville): array
    {
        $qb = $this->createQueryBuilder('r');

        if ($nom !== null && $nom !== '') {
            $qb->andWhere('r.nom LIKE :nom')->setParameter('nom', '%' . $nom . '%');
        }
        if ($codePostal !== null && $codePostal !== '') {
            $qb->andWhere('r.codePostal LIKE :cp')->setParameter('cp', $codePostal . '%');
        }
        if ($ville !== null && $ville !== '') {
            $qb->andWhere('r.ville LIKE :v')->setParameter('v', '%' . $ville . '%');
        }

        return $qb->orderBy('r.nom', 'ASC')->getQuery()->getResult();
    }
}
