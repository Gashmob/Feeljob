<?php

namespace App\Repository;

use App\Entity\SituationFamille;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SituationFamille|null find($id, $lockMode = null, $lockVersion = null)
 * @method SituationFamille|null findOneBy(array $criteria, array $orderBy = null)
 * @method SituationFamille[]    findAll()
 * @method SituationFamille[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SituationFamilleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SituationFamille::class);
    }

    // /**
    //  * @return SituationFamille[] Returns an array of SituationFamille objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SituationFamille
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
