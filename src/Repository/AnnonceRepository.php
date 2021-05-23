<?php

namespace App\Repository;

use App\Entity\Annonce;
use App\Utils;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    /**
     * @param string $departement
     * @param string $nom
     * @return Annonce[]
     */
    public function findByDepartementNom(string $departement, string $nom): array
    {
        $query = $this->createQueryBuilder('a');

        if ($departement != 'none') {
            $query = $query->leftJoin('a.adresse', 'adresse')
                ->andWhere('adresse.codePostal LIKE :departement')
                ->setParameter('departement', $departement . '%');
        }
        if ($nom != 'none') {
            $query = $query->andWhere('a.nom LIKE :nom')
                ->setParameter('nom', '%' . $nom . '%');
        }
        $query = $query->orderBy('a.updatedAt', 'DESC');

        return $query->getQuery()->getResult();
    }

    /**
     * @param float $distanceMax
     * @param string $addressFrom
     * @return Annonce[]
     */
    public function findByDistanceMax(float $distanceMax, string $addressFrom): array
    {
        $annonces = $this->createQueryBuilder('a')
            ->orderBy('a.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();

        $res = [];
        foreach ($annonces as $annonce) {
            $adresse = $annonce->getAdresse();
            if ($distanceMax == -1) {
                $res[] = $annonce;
            } elseif (!is_null($adresse)) {
                if (Utils::getDistance($addressFrom, $adresse->getRue() . ' ' . $adresse->getCodePostal() . ' ' . $adresse->getVille()) <= $distanceMax) {
                    $res[] = $annonce;
                }
            }
        }

        return $res;
    }

    /**
     * @param array $preResult
     * @param float $distanceMax
     * @param string $addressFrom
     * @return Annonce[]
     */
    public function findByDistanceMaxFromPreResultIds(array $preResult, float $distanceMax, string $addressFrom): array
    {
        $res = [];
        foreach ($preResult as $id) {
            $annonce = $this->findOneBy(['identity' => $id]);
            $adresse = $annonce->getAdresse();
            if (!is_null($adresse)) {
                if (Utils::getDistance($addressFrom, $adresse->getRue() . ' ' . $adresse->getCodePostal() . ' ' . $adresse->getVille()) <= $distanceMax) {
                    $res[] = $annonce;
                }
            }
        }

        return $res;
    }

    /**
     * @param array $ids
     * @return Annonce[]
     */
    public function findByIdentity(array $ids): array
    {
        $res = [];
        foreach ($ids as $id) {
            $res[] = $this->findOneBy(['identity' => $id]);
        }

        return $res;
    }

    // /**
    //  * @return Annonce[] Returns an array of Annonce objects
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
    public function findOneBySomeField($value): ?Annonce
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
