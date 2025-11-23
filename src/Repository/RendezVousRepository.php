<?php

namespace App\Repository;

use App\Entity\RendezVous;
use App\Entity\Donateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RendezVousRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RendezVous::class);
    }

    public function findProchainRendezVous(Donateur $donateur): ?RendezVous
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.donateur = :donateur')
            ->andWhere('r.statut = :statut')
            ->andWhere('r.dateHeureDebut > :now')
            ->setParameter('donateur', $donateur)
            ->setParameter('statut', 'Confirmé')
            ->setParameter('now', new \DateTime())
            ->orderBy('r.dateHeureDebut', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}