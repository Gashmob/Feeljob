<?php


namespace App\database\manager;


use App\database\EntityManager;
use App\database\PreparedQuery;
use App\database\Query;
use App\Entity\OffreEmploi;
use Doctrine\ORM\EntityManagerInterface;

class TypeContratManager extends Manager
{

    /**
     * @inheritDoc
     */
    public function find(int $id): ?string
    {
        $result = (new PreparedQuery('MATCH (t:TypeContrat) WHERE id(t)=$id RETURN t'))
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
        $query = 'MATCH (t:TypeContrat) WHERE ';
        foreach ($filters as $filter)
            $query .= $filter . '=' . $filters[$filter];
        $query .= ' RETURN t';

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
        return (new Query('MATCH (t:TypeContrat) RETURN t'))
            ->run()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findBy(array $filters): array
    {
        $query = 'MATCH (t:TypeContrat) WHERE ';
        foreach ($filters as $filter)
            $query .= $filter . '=' . $filters[$filter];
        $query .= ' RETURN t';

        return (new Query($query))
            ->run()
            ->getResult();
    }

    /**
     * @param EntityManagerInterface $em
     * @param string $typeContrat
     * @return OffreEmploi[]
     */
    public function findAllOffreEmploiFromTypeContrat(EntityManagerInterface $em, string $typeContrat): array
    {
        $results = (new PreparedQuery('MATCH (:' . EntityManager::TYPE_CONTRAT . ' {nom:$nom})--(o:' . EntityManager::OFFRE_EMPLOI . ') RETURN id(o) as id'))
            ->setString('nom', $typeContrat)
            ->run()
            ->getResult();

        $res = [];
        foreach ($results as $result) {
            $res[] = $em->getRepository(OffreEmploi::class)->findOneBy(['identity' => $result['id']]);
        }

        return $res;
    }
}