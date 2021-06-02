<?php

namespace App\Repository;

use App\Entity\AutoEntrepreneur;
use App\Utils;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AutoEntrepreneur|null find($id, $lockMode = null, $lockVersion = null)
 * @method AutoEntrepreneur|null findOneBy(array $criteria, array $orderBy = null)
 * @method AutoEntrepreneur[]    findAll()
 * @method AutoEntrepreneur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AutoEntrepreneurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AutoEntrepreneur::class);
    }

    /**
     * @param AutoEntrepreneur[] $preResult
     * @param float $distanceMax
     * @param string $addressFrom
     * @return AutoEntrepreneur[]
     */
    public function findByDistanceMaxFromPreResult(array $preResult, float $distanceMax, string $addressFrom): array
    {
        $res = [];
        foreach ($preResult as $result) {
            $adresse = $result->getAdresse();
            if (!($adresse->getRue() == '' && $adresse->getCodePostal() == '' && $adresse->getVille() == '')) {
                if (Utils::getDistance($addressFrom, $adresse->getRue() . ' ' . $adresse->getCodePostal() . ' ' . $adresse->getVille()) <= $distanceMax) {
                    $res[] = $result;
                }
            }
        }

        return $res;
    }

    // /**
    //  * @return AutoEntrepreneur[] Returns an array of AutoEntrepreneur objects
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
    public function findOneBySomeField($value): ?AutoEntrepreneur
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
