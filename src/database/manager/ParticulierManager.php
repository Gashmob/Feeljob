<?php


namespace App\database\manager;


use App\database\EntityManager;
use App\database\PreparedQuery;
use App\database\Query;
use App\Entity\Annonce;
use App\Entity\Particulier;
use Doctrine\ORM\EntityManagerInterface;

class ParticulierManager extends Manager
{

    /**
     * @inheritDoc
     */
    public function find(int $id): ?string
    {
        $result = (new PreparedQuery('MATCH (p:' . EntityManager::PARTICULIER . ') WHERE id(p)=$id RETURN id(p) as id'))
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
        $query = 'MATCH (p:' . EntityManager::PARTICULIER . ') WHERE ';
        foreach ($filters as $filter)
            $query .= $filter . '=' . $filters[$filter];
        $query .= ' RETURN id(p) as id';

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
        return (new Query('MATCH (p:' . EntityManager::PARTICULIER . ') RETURN id(p) as id'))
            ->run()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findBy(array $filters): array
    {
        $query = 'MATCH (p:' . EntityManager::PARTICULIER . ') WHERE ';
        foreach ($filters as $filter)
            $query .= $filter . '=' . $filters[$filter];
        $query .= ' RETURN id(p) as id';

        return (new Query($query))
            ->run()
            ->getResult();
    }

    /**
     * @param EntityManagerInterface $em
     * @param Particulier $particulier
     */
    public function create(EntityManagerInterface $em, Particulier $particulier)
    {
        $result = (new Query('CREATE (p:' . EntityManager::PARTICULIER . ') RETURN id(p) as id'))
            ->run()
            ->getOneOrNullResult();

        $particulier->setIdentity($result['id']);

        $em->persist($particulier);
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
     * @param Particulier $particulier
     */
    public function remove(EntityManagerInterface $em, Particulier $particulier)
    {
        $annonces = (new PreparedQuery('MATCH (p:' . EntityManager::PARTICULIER . ')--(a:' . EntityManager::ANNONCE . ') WHERE id(p)=$id RETURN id(a) AS id'))
            ->setInteger('id', $particulier->getIdentity())
            ->run()
            ->getResult();
        foreach ($annonces as $annonce) {
            $em->remove($em->getRepository(Annonce::class)->findOneBy(['identity' => $annonce['id']]));
        }

        (new PreparedQuery('MATCH (p:' . EntityManager::PARTICULIER . ')-[r1]-(a:' . EntityManager::ANNONCE . ')-[r2]-() WHERE id(p)=$id DELETE r1,r2,p,a'))
            ->setInteger('id', $particulier->getIdentity())
            ->run();
        $em->remove($particulier);

        $em->flush();
    }
}