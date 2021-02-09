<?php

namespace App\Repository;

use App\Entity\OffreEmploi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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
     * @param $identity
     * @param float|null $salaire
     * @param int|null $heures
     * @param bool|null $deplacement
     * @return OffreEmploi|null
     * @throws NonUniqueResultException
     */
    public function findEmploiWithFiltersAndIdentity($identity, float $salaire = null, int $heures = null, bool $deplacement = null): ?OffreEmploi
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.salaire >= :salaire')
            ->setParameter('salaire', $salaire)
            ->andWhere('o.heures = :heures')
            ->setParameter('heures', $heures)
            ->andWhere('o.deplacement = :deplacement')
            ->setParameter('deplacement', $deplacement)
            ->andWhere('o.identity = :identity')
            ->setParameter('identity', $identity)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $nom
     * @return OffreEmploi[]
     */
    public function findAllEmploiWithNameLike(string $nom): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.nom LIKE :nom')
            ->setParameter('nom', '%' . $nom . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $nom
     * @param int[] $ids
     * @return OffreEmploi[]
     */
    public function findAllEmploiWithNameLikeFromPreResultIds(string $nom, array $ids): array
    {
        $res = [];
        foreach ($ids as $id) {
            $offre = $this->findOneBy(['identity' => $id['id']]);
            if (stristr($offre->getNom(), $nom) != false) {
                $res[] = $offre;
            }
        }

        return $res;
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
