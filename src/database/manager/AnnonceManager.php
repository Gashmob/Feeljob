<?php


namespace App\database\manager;

use App\database\EntityManager;
use App\database\PreparedQuery;
use App\database\Query;
use App\Entity\Annonce;
use App\Entity\AutoEntrepreneur;
use Doctrine\ORM\EntityManagerInterface;

class AnnonceManager extends Manager
{

    /**
     * @inheritDoc
     */
    public function find(int $id): ?string
    {
        $result = (new PreparedQuery('MATCH (a:' . EntityManager::ANNONCE . ') WHERE id(a)=$id RETURN id(a) as id'))
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
        $query = 'MATCH (a:' . EntityManager::ANNONCE . ') WHERE ';
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
        return (new Query('MATCH (a:' . EntityManager::ANNONCE . ') RETURN id(a) as id'))
            ->run()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findBy(array $filters): array
    {
        $query = 'MATCH (a:' . EntityManager::ANNONCE . ') WHERE ';
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
     * @param string $metier
     * @return int|null
     */
    public function create(EntityManagerInterface $em, Annonce $annonce, int $idParticulier, string $metier): ?int
    {
        $result = (new PreparedQuery('MATCH (p:' . EntityManager::PARTICULIER . '), (m:' . EntityManager::METIER . '{nom:$metier}) WHERE id(p)=$idParticulier CREATE (p)-[:' . EntityManager::PUBLIE . ']->(a: ' . EntityManager::ANNONCE . ')-[:' . EntityManager::EST_DANS . ']->(m) RETURN id(a) AS id'))
            ->setString('metier', $metier)
            ->setInteger('idParticulier', $idParticulier)
            ->run()
            ->getOneOrNullResult();

        if ($result != null) {
            $idAnnonce = $result['id'];
            $annonce->setIdentity($idAnnonce);
            $em->persist($annonce);
            $em->flush();

            return $idAnnonce;
        }

        return null;
    }


    /**
     * @param EntityManagerInterface $em
     * @param Annonce $annonce
     */
    public function remove(EntityManagerInterface $em, Annonce $annonce)
    {
        (new PreparedQuery('MATCH (a:' . EntityManager::ANNONCE . ')-[r]-() WHERE id(a)=$id DELETE r,a'))
            ->setInteger('id', $annonce->getIdentity())
            ->run();

        $em->remove($annonce);
        $em->flush();
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $id
     * @param string $metier
     */
    public function update(EntityManagerInterface $em, int $id, string $metier)
    {
        (new PreparedQuery('MATCH (a:' . EntityManager::ANNONCE . ')-[r]-(:' . EntityManager::METIER . '), (m:' . EntityManager::METIER . ' {nom:$nom}) WHERE id(a)=$id DELETE r CREATE (a)-[:' . EntityManager::EST_DANS . ']->(m)'))
            ->setString('nom', $metier)
            ->setInteger('id', $id)
            ->run();

        $em->flush();
    }

    /**
     * @param int $idAnnonce
     * @param int $idAutoEntrepreneur
     * @return bool
     */
    public function candidate(int $idAnnonce, int $idAutoEntrepreneur): bool
    {
        $result1 = (new PreparedQuery('MATCH (a:' . EntityManager::AUTO_ENTREPRENEUR . ')-[c:' . EntityManager::CANDIDATURE . ']->(o:' . EntityManager::ANNONCE . ') WHERE id(a)=$idA AND id(o)=$idO RETURN c'))
            ->setInteger('idA', $idAutoEntrepreneur)
            ->setInteger('idO', $idAnnonce)
            ->run()
            ->getOneOrNullResult();

        $result2 = (new PreparedQuery('MATCH (o:' . EntityManager::ANNONCE . ')-[p:' . EntityManager::PROPOSITION . ']->(a:' . EntityManager::AUTO_ENTREPRENEUR . ') WHERE id(o)=$idO AND id(a)=$idA RETURN p'))
            ->setInteger('idO', $idAnnonce)
            ->setInteger('idA', $idAutoEntrepreneur)
            ->run()
            ->getOneOrNullResult();

        if (is_null($result1) && is_null($result2)) {
            (new PreparedQuery('MATCH (a:' . EntityManager::AUTO_ENTREPRENEUR . '), (o:' . EntityManager::ANNONCE . ') WHERE id(a)=$idA AND id(o)=$idO CREATE (a)-[:' . EntityManager::CANDIDATURE . ']->(o)'))
                ->setInteger('idA', $idAutoEntrepreneur)
                ->setInteger('idO', $idAnnonce)
                ->run();

            return true;
        }

        return false;
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $idAutoEntrepreneur
     * @return Annonce[]
     */
    public function getCandidature(EntityManagerInterface $em, int $idAutoEntrepreneur): array
    {
        $results = (new PreparedQuery('MATCH (a:' . EntityManager::AUTO_ENTREPRENEUR . ')-[c:' . EntityManager::CANDIDATURE . ']->(o:' . EntityManager::ANNONCE . ') WHERE id(a)=$idA AND NOT exists(c.accept) RETURN id(o) as id'))
            ->setInteger('idA', $idAutoEntrepreneur)
            ->run()
            ->getResult();

        $res = [];
        foreach ($results as $result) {
            $res[] = $em->getRepository(Annonce::class)->findOneBy(['identity' => $result['id']]);
        }

        return $res;
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $idParticulier
     * @return Annonce[]
     */
    public function getMyCandidature(EntityManagerInterface $em, int $idParticulier): array
    {
        $results = (new PreparedQuery('MATCH (p:' . EntityManager::PARTICULIER . ')--(a:' . EntityManager::ANNONCE . ')<-[ca:' . EntityManager::CANDIDATURE . ']-(c:' . EntityManager::AUTO_ENTREPRENEUR . ') WHERE id(p)=$idP AND NOT exists(ca.accept) RETURN id(a) as idA, id(c) as idC'))
            ->setInteger('idP', $idParticulier)
            ->run()
            ->getResult();

        $res = [];
        foreach ($results as $result) {
            $res[] = [
                'annonce' => $em->getRepository(Annonce::class)->findOneBy(['identity' => $result['idA']]),
                'auto' => $em->getRepository(AutoEntrepreneur::class)->findOneBy(['identity' => $result['idC']])
            ];
        }

        return $res;
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $idAutoEntrepreneur
     * @return Annonce[]
     */
    public function getAcceptedCandidature(EntityManagerInterface $em, int $idAutoEntrepreneur): array
    {
        $results = (new PreparedQuery('MATCH (a:' . EntityManager::AUTO_ENTREPRENEUR . ')-[:' . EntityManager::CANDIDATURE . ' {accept:true}]->(o:' . EntityManager::ANNONCE . ') WHERE id(a)=$idA RETURN id(o) as id'))
            ->setInteger('idA', $idAutoEntrepreneur)
            ->run()
            ->getResult();

        $res = [];
        foreach ($results as $result) {
            $res[] = $em->getRepository(Annonce::class)->findOneBy(['identity' => $result['id']]);
        }

        return $res;
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $idParticulier
     * @return Annonce[]
     */
    public function getMyAcceptedCandidature(EntityManagerInterface $em, int $idParticulier): array
    {
        $results = (new PreparedQuery('MATCH (p:' . EntityManager::PARTICULIER . ')--(a:' . EntityManager::ANNONCE . ')<-[:' . EntityManager::CANDIDATURE . ' {accept:true}]-(c:' . EntityManager::AUTO_ENTREPRENEUR . ') WHERE id(p)=$idP RETURN id(a) as idA, id(c) as idC'))
            ->setInteger('idP', $idParticulier)
            ->run()
            ->getResult();

        $res = [];
        foreach ($results as $result) {
            $res[] = [
                'annonce' => $em->getRepository(Annonce::class)->findOneBy(['identity' => $result['idA']]),
                'auto' => $em->getRepository(AutoEntrepreneur::class)->findOneBy(['identity' => $result['idC']])
            ];
        }

        return $res;
    }

    /**
     * @param int $idAnnonce
     * @param int $idAutoEntrepreneur
     */
    public function uncandidate(int $idAnnonce, int $idAutoEntrepreneur)
    {
        (new PreparedQuery('MATCH (a:' . EntityManager::AUTO_ENTREPRENEUR . ')-[c:' . EntityManager::CANDIDATURE . ']->(o:' . EntityManager::ANNONCE . ') WHERE id(a)=$idA AND id(o)=$idO DELETE c'))
            ->setInteger('idA', $idAutoEntrepreneur)
            ->setInteger('idO', $idAnnonce)
            ->run();
    }

    /**
     * @param int $idAnnonce
     * @param int $idAutoEntrepreneur
     * @return bool
     */
    public function propose(int $idAnnonce, int $idAutoEntrepreneur): bool
    {
        $result1 = (new PreparedQuery('MATCH (o:' . EntityManager::ANNONCE . ')-[p:' . EntityManager::PROPOSITION . ']->(a:' . EntityManager::AUTO_ENTREPRENEUR . ') WHERE id(a)=$idA AND id(o)=$idO RETURN p'))
            ->setInteger('idA', $idAutoEntrepreneur)
            ->setInteger('idO', $idAnnonce)
            ->run()
            ->getOneOrNullResult();

        $result2 = (new PreparedQuery('MATCH (a:' . EntityManager::AUTO_ENTREPRENEUR . ')-[c:' . EntityManager::CANDIDATURE . ']->(o:' . EntityManager::ANNONCE . ') WHERE id(a)=$idA AND id(o)=$idO RETURN c'))
            ->setInteger('idA', $idAutoEntrepreneur)
            ->setInteger('idO', $idAnnonce)
            ->run()
            ->getOneOrNullResult();

        if (is_null($result1) && is_null($result2)) {
            (new PreparedQuery('MATCH (o:' . EntityManager::ANNONCE . '), (a:' . EntityManager::AUTO_ENTREPRENEUR . ') WHERE id(a)=$idA AND id(o)=$idO CREATE (o)-[:' . EntityManager::PROPOSITION . ']->(a)'))
                ->setInteger('idA', $idAutoEntrepreneur)
                ->setInteger('idO', $idAnnonce)
                ->run();

            return true;
        }

        return false;
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $idAutoEntrepreneur
     * @return Annonce[]
     */
    public function getPropositions(EntityManagerInterface $em, int $idAutoEntrepreneur): array
    {
        $results = (new PreparedQuery('MATCH (o:' . EntityManager::ANNONCE . ')-[p:' . EntityManager::PROPOSITION . ']->(a:' . EntityManager::AUTO_ENTREPRENEUR . ') WHERE id(a)=$idA AND NOT exists(p.accept) RETURN id(o) as id'))
            ->setInteger('idA', $idAutoEntrepreneur)
            ->run()
            ->getResult();

        $res = [];
        foreach ($results as $result) {
            $res[] = $em->getRepository(Annonce::class)->findOneBy(['identity' => $result['id']]);
        }

        return $res;
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $idParticulier
     * @return Annonce[]
     */
    public function getMyPropositions(EntityManagerInterface $em, int $idParticulier): array
    {
        $results = (new PreparedQuery('MATCH (p:' . EntityManager::PARTICULIER . ')--(a:' . EntityManager::ANNONCE . ')-[pr:' . EntityManager::PROPOSITION . ']->(c:' . EntityManager::AUTO_ENTREPRENEUR . ') WHERE id(p)=$idP AND NOT exists(pr.accept) RETURN id(a) as idA, id(c) as idC'))
            ->setInteger('idP', $idParticulier)
            ->run()
            ->getResult();

        $res = [];
        foreach ($results as $result) {
            $auto = $em->getRepository(AutoEntrepreneur::class)->findOneBy(['identity' => $result['idC']]);
            if ($auto->getCarteVisite()) {
                $auto->getCarteVisite()->setAutoEntrepreneur(null);
            }

            $res[] = [
                'annonce' => $em->getRepository(Annonce::class)->findOneBy(['identity' => $result['idA']]),
                'auto' => $auto
            ];
        }

        return $res;
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $idAutoEntrepreneur
     * @return Annonce[]
     */
    public function getAcceptedPropositions(EntityManagerInterface $em, int $idAutoEntrepreneur): array
    {
        $results = (new PreparedQuery('MATCH (o:' . EntityManager::ANNONCE . ')-[:' . EntityManager::PROPOSITION . ' {accept:true}]->(a:' . EntityManager::AUTO_ENTREPRENEUR . ') WHERE id(a)=$idA RETURN id(o) as id'))
            ->setInteger('idA', $idAutoEntrepreneur)
            ->run()
            ->getResult();

        $res = [];
        foreach ($results as $result) {
            $res[] = $em->getRepository(Annonce::class)->findOneBy(['identity' => $result['id']]);
        }

        return $res;
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $idParticulier
     * @return Annonce[]
     */
    public function getMyAcceptedPropositions(EntityManagerInterface $em, int $idParticulier): array
    {
        $results = (new PreparedQuery('MATCH (p:' . EntityManager::PARTICULIER . ')--(a:' . EntityManager::ANNONCE . ')-[:' . EntityManager::PROPOSITION . ' {accept:true}]->(c:' . EntityManager::AUTO_ENTREPRENEUR . ') WHERE id(p)=$idP RETURN id(a) as idA, id(c) as idC'))
            ->setInteger('idP', $idParticulier)
            ->run()
            ->getResult();

        $res = [];
        foreach ($results as $result) {
            $auto = $em->getRepository(AutoEntrepreneur::class)->findOneBy(['identity' => $result['idC']]);
            if ($auto->getCarteVisite()) {
                $auto->getCarteVisite()->setAutoEntrepreneur(null);
            }
            $res[] = [
                'annonce' => $em->getRepository(Annonce::class)->findOneBy(['identity' => $result['idA']]),
                'auto' => $auto
            ];
        }

        return $res;
    }

    /**
     * @param int $idAnnonce
     * @param int $idAutoEntrepreneur
     */
    public function removeProposition(int $idAnnonce, int $idAutoEntrepreneur)
    {
        (new PreparedQuery('MATCH (o:' . EntityManager::ANNONCE . ')-[p:' . EntityManager::PROPOSITION . ']->(a:' . EntityManager::AUTO_ENTREPRENEUR . ') WHERE id(a)=$idA AND id(o)=$idO DELETE p'))
            ->setInteger('idA', $idAutoEntrepreneur)
            ->setInteger('idO', $idAnnonce)
            ->run();
    }

    /**
     * @param int $idAnnonce
     * @param string $secteurActivite
     */
    public function changeSecteur(int $idAnnonce, string $secteurActivite)
    {
        (new PreparedQuery('MATCH (a:' . EntityManager::ANNONCE . ')-[r]->(:' . EntityManager::SECTEUR_ACTIVITE . '), (s:' . EntityManager::SECTEUR_ACTIVITE . '{nom:$nom}) WHERE id(a)=$idA DELETE r CREATE (a)-[:' . EntityManager::TYPE . ']->(s)'))
            ->setString('nom', $secteurActivite)
            ->setInteger('idA', $idAnnonce)
            ->run();
    }

    /**
     * @param int $idAnnonce
     * @param int $idAutoEntrepreneur
     * @return bool
     */
    public function acceptCandidature(int $idAnnonce, int $idAutoEntrepreneur): bool
    {
        $result = (new PreparedQuery('MATCH (a:' . EntityManager::AUTO_ENTREPRENEUR . ')-[c:' . EntityManager::CANDIDATURE . ']->(o:' . EntityManager::ANNONCE . ') WHERE id(a)=$idA AND id(o)=$idO RETURN c'))
            ->setInteger('idA', $idAutoEntrepreneur)
            ->setInteger('idO', $idAnnonce)
            ->run()
            ->getOneOrNullResult();

        if (!is_null($result)) {
            (new PreparedQuery('MATCH (a:' . EntityManager::AUTO_ENTREPRENEUR . ')-[c:' . EntityManager::CANDIDATURE . ']->(o:' . EntityManager::ANNONCE . ') WHERE id(a)=$idA AND id(o)=$idO SET c.accept=true'))
                ->setInteger('idA', $idAutoEntrepreneur)
                ->setInteger('idO', $idAnnonce)
                ->run();

            return true;
        }

        return false;
    }

    /**
     * @param int $idAnnonce
     * @param int $idAutoEntrepreneur
     * @return bool
     */
    public function acceptProposition(int $idAnnonce, int $idAutoEntrepreneur): bool
    {
        $result = (new PreparedQuery('MATCH (o:' . EntityManager::ANNONCE . ')-[p:' . EntityManager::PROPOSITION . ']->(a:' . EntityManager::AUTO_ENTREPRENEUR . ') WHERE id(a)=$idA AND id(o)=$idO RETURN p'))
            ->setInteger('idA', $idAutoEntrepreneur)
            ->setInteger('idO', $idAnnonce)
            ->run()
            ->getOneOrNullResult();

        if (!is_null($result)) {
            (new PreparedQuery('MATCH (o:' . EntityManager::ANNONCE . ')-[p:' . EntityManager::PROPOSITION . ']->(a:' . EntityManager::AUTO_ENTREPRENEUR . ') WHERE id(a)=$idA AND id(o)=$idO SET p.accept=true'))
                ->setInteger('idA', $idAutoEntrepreneur)
                ->setInteger('idO', $idAnnonce)
                ->run();

            return true;
        }

        return false;
    }

    /**
     * @param Annonce[] $preResult
     * @param string $metier
     * @return int[]
     */
    public function getAnnoncesByMetierFromPreResult(array $preResult, string $metier): array
    {
        $res = [];

        foreach ($preResult as $result) {
            if ($metier == 'none') {
                $res[] = $result;
            } elseif ((new PreparedQuery('MATCH (a:' . EntityManager::ANNONCE . ')--(:' . EntityManager::METIER . ' {nom:$nom}) WHERE id(a)=$id RETURN id(a) AS id'))
                    ->setString('nom', $metier)
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
     * @param int $idParticulier
     * @return Annonce[]
     */
    public function findAnnoncesByParticulier(EntityManagerInterface $em, int $idParticulier): array
    {
        $results = (new PreparedQuery('MATCH (p:' . EntityManager::PARTICULIER . ')--(a:' . EntityManager::ANNONCE . ') WHERE id(p)=$id RETURN id(a) AS id'))
            ->setInteger('id', $idParticulier)
            ->run()
            ->getResult();

        $res = [];

        foreach ($results as $result) {
            $res[] = $em->getRepository(Annonce::class)->findOneBy(['identity' => $result['id']]);
        }

        return $res;
    }

    /**
     * @param int $idAnnonce
     * @param int $idParticulier
     * @return bool
     */
    public function isOwner(int $idAnnonce, int $idParticulier): bool
    {
        return (new PreparedQuery('MATCH (p:' . EntityManager::PARTICULIER . ')--(a:' . EntityManager::ANNONCE . ') WHERE id(p)=$idP AND id(a)=$idA RETURN p'))
                ->setInteger('idP', $idParticulier)
                ->setInteger('idA', $idAnnonce)
                ->run()
                ->getOneOrNullResult() != null;
    }

    /**
     * @param int $id
     * @return string|null
     */
    public function getMetier(int $id): ?string
    {
        $result = (new PreparedQuery('MATCH (a:' . EntityManager::ANNONCE . ')--(m:' . EntityManager::METIER . ') WHERE id(a)=$id RETURN m'))
            ->setInteger('id', $id)
            ->run()
            ->getOneOrNullResult();

        return $result ? $result['m']['nom'] : null;
    }
}
