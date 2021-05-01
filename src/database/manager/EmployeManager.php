<?php


namespace App\database\manager;

use App\database\EntityManager;
use App\database\PreparedQuery;
use App\database\Query;
use App\Entity\Employe;
use Doctrine\ORM\EntityManagerInterface;

class EmployeManager extends Manager
{

    /**
     * @inheritDoc
     */
    public function find(int $id): ?string
    {
        $result = (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYE . ') WHERE id(e)=$id RETURN id(e) as id'))
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
        $query = 'MATCH (e:' . EntityManager::EMPLOYE . ') WHERE ';
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
        return (new Query('MATCH (e:' . EntityManager::EMPLOYE . ') RETURN id(e) as id'))
            ->run()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findBy(array $filters): array
    {
        $query = 'MATCH (e:' . EntityManager::EMPLOYE . ') WHERE ';
        foreach ($filters as $filter)
            $query .= $filter . '=' . $filters[$filter];
        $query .= ' RETURN id(e) as id';

        return (new Query($query))
            ->run()
            ->getResult();
    }

    /**
     * @param EntityManagerInterface $em
     * @param Employe $employe
     */
    public function create(EntityManagerInterface $em, Employe $employe)
    {
        $result = (new Query('CREATE (e:' . EntityManager::EMPLOYE . ') RETURN id(e) AS id'))
            ->run()
            ->getOneOrNullResult();

        $employe->setIdentity($result['id']);
        $em->persist($employe);
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
     * @param EntityManagerInterface $em
     * @param Employe $employe
     */
    public function remove(EntityManagerInterface $em, Employe $employe)
    {
        (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYE . ')-[r]-() WHERE id(e)=$id DELETE r,e'))
            ->setInteger('id', $employe->getIdentity())
            ->run();

        $em->remove($employe);
        $em->flush();
    }
}