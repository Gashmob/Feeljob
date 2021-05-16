<?php


namespace App\database\manager;

use App\database\EntityManager;
use App\database\PreparedQuery;
use App\database\Query;
use App\Entity\Employeur;
use App\Entity\OffreEmploi;
use Doctrine\ORM\EntityManagerInterface;

class EmployeurManager extends Manager
{

    /**
     * @inheritDoc
     */
    public function find(int $id): ?string
    {
        $result = (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYEUR . ') WHERE id(e)=$id RETURN id(e) as id'))
            ->setInteger('id', $id)
            ->run()
            ->getOneOrNullResult();

        return $result == null ? null : $result['id'];
    }

    /**
     * @inheritDoc
     */
    public function findOneBy(array $filters): ?string
    {
        $query = 'MATCH (e:' . EntityManager::EMPLOYEUR . ') WHERE ';
        foreach ($filters as $filter)
            $query .= $filter . '=' . $filters[$filter];
        $query .= ' RETURN id(e) as id';

        $result = (new Query($query))
            ->run()
            ->getOneOrNullResult();

        return $result == null ? null : $result['id'];
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        return (new Query('MATCH (e:' . EntityManager::EMPLOYEUR . ') RETURN id(e) as id'))
            ->run()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findBy(array $filters): array
    {
        $query = 'MATCH (e:' . EntityManager::EMPLOYEUR . ') WHERE ';
        foreach ($filters as $filter)
            $query .= $filter . '=' . $filters[$filter];
        $query .= ' RETURN id(e) as id';

        return (new Query($query))
            ->run()
            ->getResult();
    }


    /**
     * @param EntityManagerInterface $em
     * @param Employeur $employeur
     * @param string $secteurActivite
     */
    public function create(EntityManagerInterface $em, Employeur $employeur, string $secteurActivite)
    {
        if ($secteurActivite == '') {
            $result = (new PreparedQuery('CREATE (e:' . EntityManager::EMPLOYEUR . ') RETURN id(e) as id'))
                ->run()
                ->getOneOrNullResult();
        } else {
            $result = (new PreparedQuery('MATCH (s:' . EntityManager::SECTEUR_ACTIVITE . ' {nom:$nom}) CREATE (e:' . EntityManager::EMPLOYEUR . ')-[:' . EntityManager::EST_DANS . ']->(s) RETURN id(e) as id'))
                ->setString('nom', $secteurActivite)
                ->run()
                ->getOneOrNullResult();
        }

        $employeur->setIdentity($result['id']);

        $em->persist($employeur);
        $em->flush();
    }

    /**
     * @param EntityManagerInterface $em
     * @param Employeur $employeur
     */
    public function remove(EntityManagerInterface $em, Employeur $employeur)
    {
        $offres = (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYEUR . ')--(o:' . EntityManager::OFFRE_EMPLOI . ') WHERE id(e)=$id RETURN id(o) AS id'))
            ->setInteger('id', $employeur->getIdentity())
            ->run()
            ->getOneOrNullResult();
        foreach ($offres as $offre) {
            $em->remove($em->getRepository(OffreEmploi::class)->findOneBy(['identity' => $offre['id']]));
        }

        (new PreparedQuery('MATCH ()-[r3]-(e:' . EntityManager::EMPLOYEUR . ')-[r1]-(o:' . EntityManager::OFFRE_EMPLOI . ')-[r2]-() WHERE id(e)=$id DELETE r1,r2,r3,e,o'))
            ->setInteger('id', $employeur->getIdentity())
            ->run();

        $em->remove($employeur);
        $em->flush();
    }

    /**
     * @param EntityManagerInterface $em
     */
    public function update(EntityManagerInterface $em)
    {
        $em->flush();
    }
}