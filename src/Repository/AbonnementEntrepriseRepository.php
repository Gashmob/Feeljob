<?php

namespace App\Repository;

use App\Entity\AbonnementEntreprise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AbonnementEntreprise|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbonnementEntreprise|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbonnementEntreprise[]    findAll()
 * @method AbonnementEntreprise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbonnementEntrepriseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbonnementEntreprise::class);
    }

    // /**
    //  * @return AbonnementEntreprise[] Returns an array of AbonnementEntreprise objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AbonnementEntreprise
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
