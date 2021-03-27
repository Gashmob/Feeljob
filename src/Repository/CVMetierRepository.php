<?php

namespace App\Repository;

use App\Entity\CVMetier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CVMetier|null find($id, $lockMode = null, $lockVersion = null)
 * @method CVMetier|null findOneBy(array $criteria, array $orderBy = null)
 * @method CVMetier[]    findAll()
 * @method CVMetier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CVMetierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CVMetier::class);
    }

    // /**
    //  * @return CVMetier[] Returns an array of CVMetier objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CVMetier
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
