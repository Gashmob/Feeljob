<?php

namespace App\Repository;

use App\Entity\OffreChantier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OffreChantier|null find($id, $lockMode = null, $lockVersion = null)
 * @method OffreChantier|null findOneBy(array $criteria, array $orderBy = null)
 * @method OffreChantier[]    findAll()
 * @method OffreChantier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OffreChantierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OffreChantier::class);
    }

    // /**
    //  * @return OffreChantier[] Returns an array of OffreChantier objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OffreChantier
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
