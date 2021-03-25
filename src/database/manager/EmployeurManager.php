<?php


namespace App\database\manager;

use App\database\EntityManager;
use App\database\PreparedQuery;
use App\database\Query;
use App\Entity\Employeur;
use Doctrine\ORM\EntityManagerInterface;

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
     * @param Employeur $employeur
     */
    public function create(EntityManagerInterface $em, Employeur $employeur)
    {
        $result = (new Query('CREATE (e:' . EntityManager::EMPLOYEUR . ') RETURN id(e) as id'))
            ->run()
            ->getResult();

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
        (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYEUR . ')-[r]-() WHERE id(e)=$id DELETE r,e'))
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