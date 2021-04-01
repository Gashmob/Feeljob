<?php

namespace App\Repository;

use App\Entity\AutoEntrepreneur;
use App\Entity\CarteVisite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CarteVisite|null find($id, $lockMode = null, $lockVersion = null)
 * @method CarteVisite|null findOneBy(array $criteria, array $orderBy = null)
 * @method CarteVisite[]    findAll()
 * @method CarteVisite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarteVisiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarteVisite::class);
    }

    /**
     * @param AutoEntrepreneur[] $autoEntrepreneurs
     * @return CarteVisite[]
     */
    public function findByAutoEntrepreneur(array $autoEntrepreneurs): array
    {
        $res = [];
        foreach ($autoEntrepreneurs as $autoEntrepreneur) {
            if (!is_null($autoEntrepreneur->getCarteVisite()))
                $res[] = $autoEntrepreneur->getCarteVisite();
        }

        return $res;
    }

    // /**
    //  * @return CarteVisite[] Returns an array of CarteVisite objects
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
    public function findOneBySomeField($value): ?CarteVisite
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
