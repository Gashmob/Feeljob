<?php

namespace App\Repository;

use App\Entity\CV;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CV|null find($id, $lockMode = null, $lockVersion = null)
 * @method CV|null findOneBy(array $criteria, array $orderBy = null)
 * @method CV[]    findAll()
 * @method CV[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CVRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CV::class);
    }

    /**
     * @param string[] $competences
     * @param string[] $langues
     * @param string|bool $permis
     * @return CV[]
     */
    public function findByCompetencesLanguesPermis(array $competences, array $langues, string $permis): array
    {
        $query = $this->createQueryBuilder('cv');

        if (count($competences) > 0) {
            $query->leftJoin('cv.competences', 'cv_competences')
                ->leftJoin('cv_competences.competence', 'competence');

            foreach ($competences as $competence) {
                $query->andWhere('competence.nom = :nom')
                    ->setParameter('nom', substr($competence, 0, -1))
                    ->andWhere('cv_competences.niveau >= :niveau')
                    ->setParameter('niveau', substr($competence, -1));
            }
        }

        if (count($langues) > 0) {
            $query->leftJoin('cv.langues', 'cv_langues')
                ->leftJoin('cv_langues.langue', 'langue');

            foreach ($langues as $langue) {
                $query->andWhere('langue.nom = :nom')
                    ->setParameter('nom', substr($langue, 0, -1))
                    ->andWhere('cv_langues.niveau >= :niveau')
                    ->setParameter('niveau', substr($langue, -1));
            }
        }

        if ($permis != 'none') {
            $query->andWhere('cv.permis = :permis')
                ->setParameter('permis', $permis);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @param int $idCV
     * @param int $idEmploye
     * @return bool
     */
    public function isOwner(int $idCV, int $idEmploye): bool
    {
        $cv = $this->findOneBy(['id' => $idCV]);

        return is_null($cv) ? false : $cv->getEmploye()->getIdentity() == $idEmploye;
    }

    // /**
    //  * @return CV[] Returns an array of CV objects
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
    public function findOneBySomeField($value): ?CV
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
