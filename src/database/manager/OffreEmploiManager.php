<?php


namespace App\database\manager;


use App\database\PreparedQuery;

class OffreEmploiManager extends Manager
{

    /**
     * @inheritDoc
     */
    public function find(int $id): ?string
    {
        $result = (new PreparedQuery('MATCH (o:OffreEmploi) WHERE id(o)=$id RETURN id(o) as id'))
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
        $query = 'MATCH (o:OffreEmploi) WHERE ';
        foreach ($filters as $filter)
            $query .= $filter . '=' . $filters[$filter];
        $query .= ' RETURN id(o) as id';


    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        // TODO: Implement findAll() method.
    }

    /**
     * @inheritDoc
     */
    public function findBy(array $filters): array
    {
        // TODO: Implement findBy() method.
    }
}