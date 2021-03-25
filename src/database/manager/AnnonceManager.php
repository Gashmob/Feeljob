<?php


namespace App\database\manager;

use App\database\PreparedQuery;
use App\database\Query;
use App\database\Annonce;

class AnnonceManager extends Manager
{

	/**
	 * @inheritDoc
	 */
	public function find(int $id): ?string
	{
		$result = (new PreparedQuery('MATCH (a:Annonce) WHERE id(a)=$id RETURN id(a) as id'))
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
		$query = 'MATCH (a:Annonce) WHERE ';
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
		return (new Query('MATCH (a:Annonce) RETURN id(a) as id'))
			->run()
			->getResult();
	}

	/**
	 * @inheritDoc
	 */
	public function findBy(array $filters): array
	{
		$query = 'MATCH (a:Annonce) WHERE ';
		foreach ($filters as $filter)
			$query .= $filter . '=' . $filters[$filter];
		$query .= ' RETURN id(a) as id';

		return (new Query($query))
			->run()
			->getResult();
	}


	/**
	 * @param EntityManagerInterface $em
	 * @param Annonce $annonce
	 * @param int $idParticulier
	 * @param string $secteurActivite
	 * @return int|null
	 */
	public function create(EntityManagerInterface $em,Annonce $annonce,string $idParticulier,string $secteurActivite) : ?int
	{
		$result = (new PreparedQuery('MATCH (p:' . EntityManager::PARTICULIER .'),(s:' . EntityManager::SECTEUR_ACTIVITE . '{nom:$secteur}) WHERE id(p)=$idParticulier CREATE (p)-[:' . EntityManager::PUBLIE . ']->(a: ' . EntityManager::ANNONCE .')-[:' . EntityManager::EST_DANS .']->(s) RETURN id(a) AS id'))
		->setString('secteur',$secteurActivite)
		->setInteger('idParticulier',$idParticulier)
		->run()
		->getOneOrNullResult();

		if($result !=null) {
			$idAnnonce = $result['id'];
			$annonce->setIdentity($idAnnonce);
			$em->persist($annonce);
			$em->flush();
			return $idAnnonce
		}
		return null;
	}


	/**
	 * @param EntityManagerInterface $em
	 * @param Annonce $annonce
	 */
	public function remove(EntityManagerInterface $em,Annonce $annonce){
		(new PreparedQuery('MATCH (a:' . EntityManager::ANNONCE . ')-[r]-() WHERE id(a)=$id DELETE r,a'))
		->setInteger('id',$annonce->getIdentity())
		->run();

		$em->remove($annonce);
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
