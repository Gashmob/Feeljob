<?php

namespace App\Repository;

use App\Entity\OffreEmploi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OffreEmploi|null find($id, $lockMode = null, $lockVersion = null)
 * @method OffreEmploi|null findOneBy(array $criteria, array $orderBy = null)
 * @method OffreEmploi[]    findAll()
 * @method OffreEmploi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OffreEmploiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OffreEmploi::class);
    }

    /**
     * @param string $nom
     * @param string $departement
     * @param bool|string $loge
     * @param bool|string $deplacement
     * @param bool|string $teletravail
     * @return OffreEmploi[]
     */
    public function findByDepartementLogeDeplacementTeletravailNom(string $nom, string $departement, $loge, $deplacement, $teletravail): array
    {
        $query = $this->createQueryBuilder('o');

        if ($nom != 'none') {
            $query = $query->andWhere('o.nom LIKE :nom')
                ->setParameter('nom', '%' . $nom . '%');
        }
        if ($departement != 'none') {
            $query = $query->leftJoin('o.lieu', 'a')
                ->andWhere('a.codePostal LIKE :departement')
                ->setParameter('departement', $departement . '%');
        }
        if ($loge != 'none') {
            $query = $query->andWhere('o.loge = :loge')
                ->setParameter('loge', $loge == 'true');
        }
        if ($deplacement != 'none') {
            $query = $query->andWhere('o.deplacement = :deplacement')
                ->setParameter('deplacement', $deplacement == 'true');
        }
        if ($teletravail != 'none') {
            $query = $query->andWhere('o.teletravail = :teletravail')
                ->setParameter('teletravail', $teletravail == 'true');
        }

        $query = $query->andWhere('o.nbPostes > 0');

        $query = $query->orderBy('o.updatedAt', 'DESC');

        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return OffreEmploi[] Returns an array of OffreEmploi objects
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
    public function findOneBySomeField($value): ?OffreEmploi
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
