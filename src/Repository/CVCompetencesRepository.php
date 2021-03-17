<?php

namespace App\Repository;

use App\Entity\CVCompetences;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CVCompetences|null find($id, $lockMode = null, $lockVersion = null)
 * @method CVCompetences|null findOneBy(array $criteria, array $orderBy = null)
 * @method CVCompetences[]    findAll()
 * @method CVCompetences[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CVCompetencesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CVCompetences::class);
    }

    // /**
    //  * @return CVCompetences[] Returns an array of CVCompetences objects
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
    public function findOneBySomeField($value): ?CVCompetences
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
