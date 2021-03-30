<?php


namespace App\database\manager;


use App\database\EntityManager;
use App\database\PreparedQuery;
use App\database\Query;
use App\Entity\OffreEmploi;
use Doctrine\ORM\EntityManagerInterface;

class OffreEmploiManager extends Manager
{

    /**
     * @inheritDoc
     */
    public function find(int $id): ?string
    {
        $result = (new PreparedQuery('MATCH (o:' . EntityManager::OFFRE_EMPLOI . ') WHERE id(o)=$id RETURN id(o) as id'))
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
        $query = 'MATCH (o:' . EntityManager::OFFRE_EMPLOI . ') WHERE ';
        foreach ($filters as $filter)
            $query .= $filter . '=' . $filters[$filter];
        $query .= ' RETURN id(o) as id';

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
        return (new Query('MATCH (o:' . EntityManager::OFFRE_EMPLOI . ') RETURN id(o) as id'))
            ->run()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findBy(array $filters): array
    {
        $query = 'MATCH (o:' . EntityManager::OFFRE_EMPLOI . ') WHERE ';
        foreach ($filters as $filter)
            $query .= $filter . '=' . $filters[$filter];
        $query .= ' RETURN id(o) as id';

        return (new Query($query))
            ->run()
            ->getResult();
    }

    /**
     * @param EntityManagerInterface $em
     * @param OffreEmploi $offre
     * @param int $idEmployeur
     * @param string $typeContrat
     * @return int|null
     */
    public function create(EntityManagerInterface $em, OffreEmploi $offre, int $idEmployeur, string $typeContrat): ?int
    {
        $result = (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYEUR . '), (t:' . EntityManager::TYPE_CONTRAT . ' {nom:$type}) WHERE id(e)=$idE CREATE (e)-[:' . EntityManager::PUBLIE . ']->(o:' . EntityManager::OFFRE_EMPLOI . ')-[:' . EntityManager::TYPE . ']->(t) RETURN id(o) AS id'))
            ->setString('type', $typeContrat)
            ->setInteger('idE', $idEmployeur)
            ->run()
            ->getOneOrNullResult();

        if ($result != null) {
            $idOffre = $result['id'];
            $offre->setIdentity($idOffre);
            $em->persist($offre);
            $em->flush();

            return $idOffre;
        }

        return null;
    }

    /**
     * @param EntityManagerInterface $em
     * @param OffreEmploi $offre
     */
    public function remove(EntityManagerInterface $em, OffreEmploi $offre)
    {
        (new PreparedQuery('MATCH (o:' . EntityManager::OFFRE_EMPLOI . ')-[r]-() WHERE id(o)=$id DELETE r,o'))
            ->setInteger('id', $offre->getIdentity())
            ->run();

        $em->remove($offre);
        $em->flush();
    }

    /**
     * @param EntityManagerInterface $em
     * @param OffreEmploi $offre
     * @param string $typeContrat
     */
    public function update(EntityManagerInterface $em, OffreEmploi $offre, string $typeContrat)
    {
        (new PreparedQuery('MATCH (o:' . EntityManager::OFFRE_EMPLOI . ')-[r]-(:' . EntityManager::TYPE_CONTRAT . '), (t:' . EntityManager::TYPE_CONTRAT . ' {nom:$nom}) WHERE id(o)=$id DELETE r CREATE (o)-[:' . EntityManager::TYPE . ']->(t)'))
            ->setString('nom', $typeContrat)
            ->setInteger('id', $offre->getIdentity())
            ->run();

        $em->flush();
    }

    /**
     * @param int $idOffre
     * @param int $idEmploye
     * @return bool
     */
    public function addToFavoris(int $idOffre, int $idEmploye): bool
    {
        $result = (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYE . ')-[f:' . EntityManager::FAVORI . ']->(o:' . EntityManager::OFFRE_EMPLOI . ') WHERE id(e)=$idE AND id(o)=$idO RETURN f'))
            ->setInteger('idE', $idEmploye)
            ->setInteger('idO', $idOffre)
            ->run()
            ->getOneOrNullResult();

        if (is_null($result)) {
            (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYE . '), (o:' . EntityManager::OFFRE_EMPLOI . ') WHERE id(e)=$idE AND id(o)=$idO CREATE (e)-[:' . EntityManager::FAVORI . ']->(o)'))
                ->setInteger('idE', $idEmploye)
                ->setInteger('idO', $idOffre)
                ->run();

            return true;
        }

        return false;
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $idEmploye
     * @return OffreEmploi[]
     */
    public function getFavoris(EntityManagerInterface $em, int $idEmploye): array
    {
        $results = (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYE . ')-[:' . EntityManager::FAVORI . ']->(o:' . EntityManager::OFFRE_EMPLOI . ') WHERE id(e)=$idE RETURN id(o) as id'))
            ->setInteger('idE', $idEmploye)
            ->run()
            ->getResult();

        $res = [];
        foreach ($results as $result) {
            $res[] = $em->getRepository(OffreEmploi::class)->findOneBy(['identity' => $result['id']]);
        }

        return $res;
    }

    /**
     * @param int $idOffre
     * @param int $idEmploye
     */
    public function removeFavoris(int $idOffre, int $idEmploye)
    {
        (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYE . ')-[f:' . EntityManager::FAVORI . ']->(o:' . EntityManager::OFFRE_EMPLOI . ') WHERE id(e)=$idE AND id(o)=$idO DELETE f'))
            ->setInteger('idE', $idEmploye)
            ->setInteger('idO', $idOffre)
            ->run();
    }

    /**
     * @param int $idOffre
     * @param int $idEmploye
     * @return bool
     */
    public function candidate(int $idOffre, int $idEmploye): bool
    {
        $result1 = (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYE . ')-[c:' . EntityManager::CANDIDATURE . ']->(o:' . EntityManager::OFFRE_EMPLOI . ') WHERE id(e)=$idE AND id(o)=$idO RETURN c'))
            ->setInteger('idE', $idEmploye)
            ->setInteger('idO', $idOffre)
            ->run()
            ->getOneOrNullResult();

        $result2 = (new PreparedQuery('MATCH (o:' . EntityManager::OFFRE_EMPLOI . ')-[p:' . EntityManager::PROPOSITION . ']->(e:' . EntityManager::EMPLOYE . ') WHERE id(o)=$idO AND id(e)=$idE RETURN p'))
            ->setInteger('idO', $idOffre)
            ->setInteger('idE', $idEmploye)
            ->run()
            ->getOneOrNullResult();

        if (is_null($result1) && is_null($result2)) {
            (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYE . '), (o:' . EntityManager::OFFRE_EMPLOI . ') WHERE id(e)=$idE AND id(o)=$idO CREATE (e)-[:' . EntityManager::CANDIDATURE . ']->(o)'))
                ->setInteger('idE', $idEmploye)
                ->setInteger('idO', $idOffre)
                ->run();

            return true;
        }

        return false;
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $idEmploye
     * @return OffreEmploi[]
     */
    public function getCandidature(EntityManagerInterface $em, int $idEmploye): array
    {
        $results = (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYE . ')-[:' . EntityManager::CANDIDATURE . ']->(o:' . EntityManager::OFFRE_EMPLOI . ') WHERE id(e)=$idE RETURN id(o) as id'))
            ->setInteger('idE', $idEmploye)
            ->run()
            ->getResult();

        $res = [];
        foreach ($results as $result) {
            $res[] = $em->getRepository(OffreEmploi::class)->findOneBy(['identity' => $result['id']]);
        }

        return $res;
    }

    /**
     * @param int $idOffre
     * @param int $idEmploye
     */
    public function uncandidate(int $idOffre, int $idEmploye)
    {
        (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYE . ')-[c:' . EntityManager::CANDIDATURE . ']->(o:' . EntityManager::OFFRE_EMPLOI . ') WHERE id(e)=$idE AND id(o)=$idO DELETE c'))
            ->setInteger('idE', $idEmploye)
            ->setInteger('idO', $idOffre)
            ->run();
    }

    /**
     * @param int $idOffre
     * @param int $idEmploye
     * @return bool
     */
    public function propose(int $idOffre, int $idEmploye): bool
    {
        $result1 = (new PreparedQuery('MATCH (o:' . EntityManager::OFFRE_EMPLOI . ')-[p:' . EntityManager::PROPOSITION . ']->(e:' . EntityManager::EMPLOYE . ') WHERE id(e)=$idE AND id(o)=$idO RETURN p'))
            ->setInteger('idE', $idEmploye)
            ->setInteger('idO', $idOffre)
            ->run()
            ->getOneOrNullResult();

        $result2 = (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYE . ')-[c:' . EntityManager::CANDIDATURE . ']->(o:' . EntityManager::OFFRE_EMPLOI . ') WHERE id(e)=$idE AND id(o)=$idO RETURN c'))
            ->setInteger('idE', $idEmploye)
            ->setInteger('idO', $idOffre)
            ->run()
            ->getOneOrNullResult();

        if (is_null($result1) && is_null($result2)) {
            (new PreparedQuery('MATCH (o:' . EntityManager::OFFRE_EMPLOI . '), (e:' . EntityManager::EMPLOYE . ') WHERE id(e)=$idE AND id(o)=$idO CREATE (o)-[:' . EntityManager::PROPOSITION . ']->(e)'))
                ->setInteger('idE', $idEmploye)
                ->setInteger('idO', $idOffre)
                ->run();

            return true;
        }

        return false;
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $idEmploye
     * @return OffreEmploi[]
     */
    public function getPropositions(EntityManagerInterface $em, int $idEmploye): array
    {
        $results = (new PreparedQuery('MATCH (o:' . EntityManager::OFFRE_EMPLOI . ')-[:' . EntityManager::PROPOSITION . ']->(e:' . EntityManager::EMPLOYE . ') WHERE id(e)=$idE RETURN id(o) as id'))
            ->setInteger('idE', $idEmploye)
            ->run()
            ->getResult();

        $res = [];
        foreach ($results as $result) {
            $res[] = $em->getRepository(OffreEmploi::class)->findOneBy(['identity' => $result['id']]);
        }

        return $res;
    }

    /**
     * @param int $idOffre
     * @param int $idEmploye
     */
    public function removeProposition(int $idOffre, int $idEmploye)
    {
        (new PreparedQuery('MATCH (o:' . EntityManager::OFFRE_EMPLOI . ')-[p:' . EntityManager::PROPOSITION . ']->(e:' . EntityManager::EMPLOYE . ') WHERE id(e)=$idE AND id(o)=$idO DELETE p'))
            ->setInteger('idE', $idEmploye)
            ->setInteger('idO', $idOffre)
            ->run();
    }

    /**
     * @param int $idOffre
     * @param string $typeContrat
     */
    public function changeType(int $idOffre, string $typeContrat)
    {
        (new PreparedQuery('MATCH (o:' . EntityManager::OFFRE_EMPLOI . ')-[r]->(:' . EntityManager::TYPE_CONTRAT . '), (t:' . EntityManager::TYPE_CONTRAT . '{nom:$nom}) WHERE id(o)=$idO DELETE r CREATE (o)-[:' . EntityManager::TYPE . ']->(t)'))
            ->setString('nom', $typeContrat)
            ->setInteger('idO', $idOffre)
            ->run();
    }

    /**
     * @param int $idOffre
     * @param int $idEmploye
     * @return bool
     */
    public function acceptCandidature(int $idOffre, int $idEmploye): bool
    {
        $result = (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYE . ')-[c:' . EntityManager::CANDIDATURE . ']->(o:' . EntityManager::OFFRE_EMPLOI . ') WHERE id(e)=$idE AND id(o)=$idO RETURN c'))
            ->setInteger('idE', $idEmploye)
            ->setInteger('idO', $idOffre)
            ->run()
            ->getOneOrNullResult();

        if (!is_null($result)) {
            (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYE . ')-[c:' . EntityManager::CANDIDATURE . ']->(o:' . EntityManager::OFFRE_EMPLOI . ') WHERE id(e)=$idE AND id(o)=$idO SET c.accept=true'))
                ->setInteger('idE', $idEmploye)
                ->setInteger('idO', $idOffre)
                ->run();

            return true;
        }

        return false;
    }

    /**
     * @param int $idOffre
     * @param int $idEmploye
     * @return bool
     */
    public function acceptProposition(int $idOffre, int $idEmploye): bool
    {
        $result = (new PreparedQuery('MATCH (o:' . EntityManager::OFFRE_EMPLOI . ')-[p:' . EntityManager::PROPOSITION . ']->(e:' . EntityManager::EMPLOYE . ') WHERE id(e)=$idE AND id(o)=$idO RETURN p'))
            ->setInteger('idE', $idEmploye)
            ->setInteger('idO', $idOffre)
            ->run()
            ->getOneOrNullResult();

        if (!is_null($result)) {
            (new PreparedQuery('MATCH (o:' . EntityManager::OFFRE_EMPLOI . ')-[p:' . EntityManager::PROPOSITION . ']->(e:' . EntityManager::EMPLOYE . ') WHERE id(e)=$idE AND id(o)=$idO SET p.accept=true'))
                ->setInteger('idE', $idEmploye)
                ->setInteger('idO', $idOffre)
                ->run();

            return true;
        }

        return false;
    }

    /**
     * @param OffreEmploi[] $preResult
     * @param string $typeContrat
     * @return OffreEmploi[]
     */
    public function findOffreEmploiByTypeContratFromPreResult(array $preResult, string $typeContrat): array
    {
        $res = [];
        foreach ($preResult as $result) {
            if ((new PreparedQuery('MATCH (o:' . EntityManager::OFFRE_EMPLOI . ')--(t:' . EntityManager::TYPE_CONTRAT . ' {nom:$nom}) WHERE id(o)=$id RETURN id(o) AS id'))
                    ->setString('nom', $typeContrat)
                    ->setInteger('id', $result->getIdentity())
                    ->run()
                    ->getOneOrNullResult() != null) {
                $res[] = $result;
            }
        }

        return $res;
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $idEmployeur
     * @return OffreEmploi[]
     */
    public function findOffresEmploiByEmployeur(EntityManagerInterface $em, int $idEmployeur): array
    {
        $results = (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYEUR . ')--(o:' . EntityManager::OFFRE_EMPLOI .  ') WHERE id(e)=$id RETURN id(o) AS id'))
            ->setInteger('id', $idEmployeur)
            ->run()
            ->getResult();

        $res = [];

        foreach ($results as $result) {
            $res[] = $em->getRepository(OffreEmploi::class)->findOneBy(['identity' => $result['id']]);
        }

        return $res;
    }
}