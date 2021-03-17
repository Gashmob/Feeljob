<?php


namespace App\database\manager;


use App\database\PreparedQuery;
use App\database\Query;

class SecteurActiviteManager extends Manager
{

    /**
     * @inheritDoc
     */
    public function find(int $id): ?string
    {
        $result = (new PreparedQuery('MATCH (s:SecteurActivite) WHERE id(s)=$id RETURN s'))
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
        $query = 'MATCH (s:SecteurActivite) WHERE ';
        foreach ($filters as $filter)
            $query .= $filter . '=' . $filters[$filter];
        $query .= ' RETURN s';

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
        return (new Query('MATCH (s:SecteurActivite) RETURN s'))
            ->run()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findBy(array $filters): array
    {
        $query = 'MATCH (s:SecteurActivite) WHERE ';
        foreach ($filters as $filter)
            $query .= $filter . '=' . $filters[$filter];
        $query .= ' RETURN s';

        return (new Query($query))
            ->run()
            ->getResult();
    }
}