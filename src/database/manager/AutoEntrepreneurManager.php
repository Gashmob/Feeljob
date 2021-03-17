<?php


namespace App\database\manager;

use App\database\PreparedQuery;
use App\database\Query;

class AutoEntrepreneurManager extends Manager
{

    /**
     * @inheritDoc
     */
    public function find(int $id): string
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
    public function findOneBy(array $filters): string
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
}