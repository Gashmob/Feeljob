<?php

namespace App\Repository;

use App\Entity\CVDiplome;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CVDiplome|null find($id, $lockMode = null, $lockVersion = null)
 * @method CVDiplome|null findOneBy(array $criteria, array $orderBy = null)
 * @method CVDiplome[]    findAll()
 * @method CVDiplome[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CVDiplomeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CVDiplome::class);
    }

    // /**
    //  * @return CVDiplome[] Returns an array of CVDiplome objects
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
    public function findOneBySomeField($value): ?CVDiplome
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
