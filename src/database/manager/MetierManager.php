<?php


namespace App\database\manager;


use App\database\EntityManager;
use App\database\PreparedQuery;
use App\database\Query;

class MetierManager extends Manager
{

    /**
     * @inheritDoc
     */
    public function find(int $id): ?string
    {
        $result = (new PreparedQuery('MATCH (m:' . EntityManager::METIER . ') WHERE id(m)=$id RETURN m'))
            ->setInteger('id', $id)
            ->run()
            ->getOneOrNullResult();

        return $result == null ? null : $result['nom'];
    }

    /**
     * @inheritDoc
     */
    public function findOneBy(array $filters): ?string
    {
        $query = 'MATCH (m:' . EntityManager::METIER . ') WHERE ';
        foreach ($filters as $filter)
            $query .= $filter . '=' . $filters[$filter];
        $query .= ' RETURN m';

        $result = (new Query($query))
            ->run()
            ->getOneOrNullResult();

        return $result == null ? null : $result['nom'];
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        return (new Query('MATCH (m:' . EntityManager::METIER . ') RETURN m'))
            ->run()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findBy(array $filters): array
    {
        $query = 'MATCH (m:' . EntityManager::METIER . ') WHERE ';
        foreach ($filters as $filter)
            $query .= $filter . '=' . $filters[$filter];
        $query .= ' RETURN m';

        return (new Query($query))
            ->run()
            ->getResult();
    }

    /**
     * @param string $nom
     * @param string $secteur
     */
    public function create(string $nom, string $secteur)
    {
        (new PreparedQuery('MATCH (s:' . EntityManager::SECTEUR_ACTIVITE . ' {nom:$secteur}) CREATE (s)-[:' . EntityManager::EST_DANS . ']->(:' . EntityManager::METIER . ' {nom:$nom})'))
            ->setString('secteur', $secteur)
            ->setString('nom', $nom)
            ->run();
    }

    /**
     * @param int $id
     * @param string $nom
     */
    public function update(int $id, string $nom)
    {
        (new PreparedQuery('MATCH (m:' . EntityManager::METIER . ') WHERE id(m)=$id SET m.nom=$nom'))
            ->setInteger('id', $id)
            ->setString('nom', $nom)
            ->run();
    }

    /**
     * @param int $id
     */
    public function remove(int $id)
    {
        (new PreparedQuery('MATCH (m:' . EntityManager::METIER . ')-[r]-() WHERE id(m)=$id DELETE r,m'))
            ->setInteger('id', $id)
            ->run();
    }

    /**
     * @return string[]
     */
    public function findAllNames(): array
    {
        $res = [];
        $results = $this->findAll();

        foreach ($results as $result) {
            $res[] = $result['m']['nom'];
        }

        return $res;
    }

    /**
     * @param string $secteur
     * @return string[]
     */
    public function findAllNamesBySecteurActivite(string $secteur): array
    {
        $res = [];
        $results = (new PreparedQuery('MATCH (:' . EntityManager::SECTEUR_ACTIVITE . ' {nom:$nom})--(m:' . EntityManager::METIER . ') RETURN m'))
            ->setString('nom', $secteur)
            ->run()
            ->getResult();

        foreach ($results as $result) {
            $res[] = $result['m']['nom'];
        }

        return $res;
    }

    /**
     * @return array
     */
    public function findAllNamesWithSecteurActivite(): array
    {
        $secteurs = (new SecteurActiviteManager())->findAllNames();

        $res = [];
        foreach ($secteurs as $secteur) {
            $res[] = [$secteur => $this->findAllNamesBySecteurActivite($secteur)];
        }

        return $res;
    }
}