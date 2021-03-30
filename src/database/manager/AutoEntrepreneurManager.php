<?php


namespace App\database\manager;

use App\database\EntityManager;
use App\database\PreparedQuery;
use App\database\Query;
use App\Entity\AutoEntrepreneur;
use Doctrine\ORM\EntityManagerInterface;

class AutoEntrepreneurManager extends Manager
{

    /**
     * @inheritDoc
     */
    public function find(int $id): ?string
    {
        $result = (new PreparedQuery('MATCH (a:AutoEntrepreneur) WHERE id(a)=$id RETURN id(a) as id'))
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
        $query = 'MATCH (a:AutoEntrepreneur) WHERE ';
        foreach ($filters as $filter)
            $query .= $filter . '=' . $filters[$filter];
        $query .= ' RETURN id(a) as id';

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
        return (new Query('MATCH (a:AutoEntrepreneur) RETURN id(a) as id'))
            ->run()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findBy(array $filters): array
    {
        $query = 'MATCH (a:AutoEntrepreneur) WHERE ';
        foreach ($filters as $filter)
            $query .= $filter . '=' . $filters[$filter];
        $query .= ' RETURN id(a) as id';

        return (new Query($query))
            ->run()
            ->getResult();
    }


    /**
     * @param EntityManagerInterface $em
     * @param AutoEntrepreneur $autoEntrepreneur
     * @param string $secteurActivite
     */
    public function create(EntityManagerInterface $em, AutoEntrepreneur $autoEntrepreneur, string $secteurActivite)
    {
        $result = (new PreparedQuery('MATCH (s:' . EntityManager::SECTEUR_ACTIVITE . ' {nom:$nom}) CREATE (a:' . EntityManager::AUTO_ENTREPRENEUR . ')-[:' . EntityManager::EST_DANS . ']->(s) RETURN id(a) as id'))
            ->setString('nom', $secteurActivite)
            ->run()
            ->getOneOrNullResult();

        $autoEntrepreneur->setIdentity($result['id']);

        $em->persist($autoEntrepreneur);
        $em->flush();
    }

    /**
     * @param EntityManagerInterface $em
     * @param AutoEntrepreneur $autoEntrepreneur
     */
    public function remove(EntityManagerInterface $em, AutoEntrepreneur $autoEntrepreneur)
    {
        (new PreparedQuery('MATCH (a:' . EntityManager::AUTO_ENTREPRENEUR . ')-[r]-() WHERE id(a)=$id DELETE r,a'))
            ->setInteger('id', $autoEntrepreneur->getIdentity())
            ->run();

        $em->remove($autoEntrepreneur);
        $em->flush();
    }

    /**
     * @param EntityManagerInterface $em
     */
    public function update(EntityManagerInterface $em)
    {
        $em->flush();
    }

    /**
     * @param AutoEntrepreneur[] $preResult
     * @param string $secteur
     * @return AutoEntrepreneur[]
     */
    public function findBySecteurActiviteFromPreResult(array $preResult, string $secteur): array
    {
        $res = [];

        foreach ($preResult as $result) {
            if ((new PreparedQuery('MATCH (a:' . EntityManager::AUTO_ENTREPRENEUR . ')--(s:' . EntityManager::SECTEUR_ACTIVITE .' {nom:$nom}) WHERE id(a)=$id RETURN a'))
                ->setString('nom', $secteur)
                ->setInteger('id', $result->getIdentity())
                ->run()
                ->getOneOrNullResult() != null) {
                $res[] = $result;
            }
        }

        return $res;
    }
}