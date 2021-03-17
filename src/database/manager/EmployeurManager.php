<?php


namespace App\database\manager;

use App\database\PreparedQuery;
use App\database\Query;

class EmployeurManager extends Manager
{

    /**
     * @inheritDoc
     */
    public function find(int $id): string
    {
        $result = (new PreparedQuery('MATCH (e:Employeur) WHERE id(e)=$id RETURN id(e) as id'))
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
        $query = 'MATCH (e:Employeur) WHERE ';
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
        return (new Query('MATCH (e:Employeur) RETURN id(e) as id'))
	->run()
	->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findBy(array $filters): array
    {
        $query = 'MATCH (e:Employeur) WHERE ';
		foreach ($filters as $filter)
			$query .= $filter . '=' . $filters[$filter];
		$query .= ' RETURN id(e) as id';

		return (new Query($query))
			->run()
			->getResult();
    }
}