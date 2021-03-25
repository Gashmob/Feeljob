<?php


namespace App\database\manager;

use App\database\PreparedQuery;
use App\database\Query;

class EmployeurManager extends Manager
{

    /**
     * @inheritDoc
     */
    public function find(int $id): ?string
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
    public function findOneBy(array $filters): ?string
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


    /**
     * @param EntityManagerInterface $em
     * @param Entrepreneur $entrepreneur
     */
    public function create(EntityManagerInterface $em,Entrepreneur $entrepreneur)
    {
        $result = (new Query('CREATE (e:' . EntityManager::ENTREPRENEUR . ') RETURN id(e) as id'))
            ->run()
            ->getResult();

        $entrepreneur->setIdentity($result['id']);

        $em->persist($entrepreneur);
        $em->flush(); 
    }

    /**
     * @param EntityManagerInterface $em
     * @param Entrepreneur $entrepreneur
     */
    public function remove(EntityManagerInterface $em,Entrepreneur $entrepreneur)
    {
        (new PreparedQuery('MATCH (e:' . EntityManager::ENTREPRENEUR . ')-[r]-() WHERE id(e)=$id DELETE r,e'))
            ->setInteger('id',$entrepreneur->getIdentity())
            ->run();

        $em->remove($entrepreneur);
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