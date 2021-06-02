<?php


namespace App\database\manager;


use App\database\EntityManager;
use App\database\PreparedQuery;
use App\database\Query;
use App\Entity\Employe;
use App\Entity\Employeur;
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
     * @param string $metier
     * @return int|null
     */
    public function create(EntityManagerInterface $em, OffreEmploi $offre, int $idEmployeur, string $typeContrat, string $metier): ?int
    {
        $result = (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYEUR . '), (t:' . EntityManager::TYPE_CONTRAT . ' {nom:$type}), (m:' . EntityManager::METIER . ' {nom:$metier}) WHERE id(e)=$idE CREATE (e)-[:' . EntityManager::PUBLIE . ']->(o:' . EntityManager::OFFRE_EMPLOI . ')-[:' . EntityManager::TYPE . ']->(t), (o)-[:' . EntityManager::EST_DANS . ']->(m) RETURN id(o) AS id'))
            ->setString('type', $typeContrat)
            ->setString('metier', $metier)
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
     * @param string $metier
     */
    public function update(EntityManagerInterface $em, OffreEmploi $offre, string $typeContrat, string $metier)
    {
        (new PreparedQuery('MATCH (:' . EntityManager::METIER . ')-[r1]-(o:' . EntityManager::OFFRE_EMPLOI . ')-[r2]-(:' . EntityManager::TYPE_CONTRAT . '), (t:' . EntityManager::TYPE_CONTRAT . ' {nom:$type}), (m:' . EntityManager::METIER . ' {nom:$metier}) WHERE id(o)=$id DELETE r1,r2 CREATE (m)<-[:' . EntityManager::EST_DANS . ']-(o)-[:' . EntityManager::TYPE . ']->(t)'))
            ->setString('type', $typeContrat)
            ->setString('metier', $metier)
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
        $results = (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYE . ')-[c:' . EntityManager::CANDIDATURE . ']->(o:' . EntityManager::OFFRE_EMPLOI . ') WHERE id(e)=$idE AND NOT exists(c.accept) RETURN id(o) as id'))
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
     * @param EntityManagerInterface $em
     * @param int $idEmployeur
     * @return OffreEmploi[]
     */
    public function getMyCandidature(EntityManagerInterface $em, int $idEmployeur): array
    {
        $results = (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYEUR . ')--(o:' . EntityManager::OFFRE_EMPLOI . ')<-[ca:' . EntityManager::CANDIDATURE . ']-(c:' . EntityManager::EMPLOYE . ') WHERE id(e)=$idE AND NOT exists(ca.accept) RETURN id(o) as idO, id(c) as idC'))
            ->setInteger('idE', $idEmployeur)
            ->run()
            ->getResult();

        $res = [];
        foreach ($results as $result) {
            $employe = $em->getRepository(Employe::class)->findOneBy(['identity' => $result['idC']]);
            $cv = $employe->getCV();
            if (!is_null($cv)) {
                if (!is_null($cv->getEmploye())) {
                    $cv->setEmploye(null);
                }
                foreach ($cv->getCompetences() as $competence) {
                    $competence->setCV(null);
                }
                foreach ($cv->getMetiers() as $metier) {
                    $metier->setCV(null);
                }
                foreach ($cv->getDiplomes() as $diplome) {
                    $diplome->setCV(null);
                }
                foreach ($cv->getLangues() as $langue) {
                    $langue->setCV(null);
                }
            }

            $res[] = [
                'offre' => $em->getRepository(OffreEmploi::class)->findOneBy(['identity' => $result['idO']]),
                'employe' => $employe,
                'cv' => $cv,
            ];
        }

        return $res;
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $idEmploye
     * @return OffreEmploi[]
     */
    public function getAcceptedCandidature(EntityManagerInterface $em, int $idEmploye): array
    {
        $results = (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYE . ')-[:' . EntityManager::CANDIDATURE . ' {accept:true}]->(o:' . EntityManager::OFFRE_EMPLOI . ') WHERE id(e)=$idE RETURN id(o) as id'))
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
     * @param EntityManagerInterface $em
     * @param int $idEmployeur
     * @return OffreEmploi[]
     */
    public function getMyAcceptedCandidature(EntityManagerInterface $em, int $idEmployeur): array
    {
        $results = (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYEUR . ')--(o:' . EntityManager::OFFRE_EMPLOI . ')<-[:' . EntityManager::CANDIDATURE . ' {accept:true}]-(c:' . EntityManager::EMPLOYE . ') WHERE id(e)=$idE RETURN id(o) as idO, id(c) as idC'))
            ->setInteger('idE', $idEmployeur)
            ->run()
            ->getResult();

        $res = [];
        foreach ($results as $result) {
            $employe = $em->getRepository(Employe::class)->findOneBy(['identity' => $result['idC']]);
            $cv = $employe->getCV();
            if (!is_null($cv)) {
                if (!is_null($cv->getEmploye())) {
                    $cv->setEmploye(null);
                }
                foreach ($cv->getCompetences() as $competence) {
                    $competence->setCV(null);
                }
                foreach ($cv->getMetiers() as $metier) {
                    $metier->setCV(null);
                }
                foreach ($cv->getDiplomes() as $diplome) {
                    $diplome->setCV(null);
                }
                foreach ($cv->getLangues() as $langue) {
                    $langue->setCV(null);
                }
            }

            $res[] = [
                'offre' => $em->getRepository(OffreEmploi::class)->findOneBy(['identity' => $result['idO']]),
                'employe' => $employe,
                'cv' => $cv,
            ];
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
        $results = (new PreparedQuery('MATCH (o:' . EntityManager::OFFRE_EMPLOI . ')-[p:' . EntityManager::PROPOSITION . ']->(e:' . EntityManager::EMPLOYE . ') WHERE id(e)=$idE AND NOT exists(p.accept) RETURN id(o) as id'))
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
     * @param EntityManagerInterface $em
     * @param int $idEmployeur
     * @return OffreEmploi[]
     */
    public function getMyPropositions(EntityManagerInterface $em, int $idEmployeur): array
    {
        $results = (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYEUR . ')--(o:' . EntityManager::OFFRE_EMPLOI . ')-[p:' . EntityManager::PROPOSITION . ']->(c:' . EntityManager::EMPLOYE . ') WHERE id(e)=$idE AND NOT exists(p.accept) RETURN id(o) as idO, id(c) as idC'))
            ->setInteger('idE', $idEmployeur)
            ->run()
            ->getResult();

        $res = [];
        foreach ($results as $result) {
            $employe = $em->getRepository(Employe::class)->findOneBy(['identity' => $result['idC']]);
            $cv = $employe->getCV();
            if (!is_null($cv)) {
                if (!is_null($cv->getEmploye())) {
                    $cv->setEmploye(null);
                }
                foreach ($cv->getCompetences() as $competence) {
                    $competence->setCV(null);
                }
                foreach ($cv->getMetiers() as $metier) {
                    $metier->setCV(null);
                }
                foreach ($cv->getDiplomes() as $diplome) {
                    $diplome->setCV(null);
                }
                foreach ($cv->getLangues() as $langue) {
                    $langue->setCV(null);
                }
            }

            $res[] = [
                'offre' => $em->getRepository(OffreEmploi::class)->findOneBy(['identity' => $result['idO']]),
                'employe' => $employe,
                'cv' => $cv,
            ];
        }

        return $res;
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $idEmploye
     * @return OffreEmploi[]
     */
    public function getAcceptedPropositions(EntityManagerInterface $em, int $idEmploye): array
    {
        $results = (new PreparedQuery('MATCH (o:' . EntityManager::OFFRE_EMPLOI . ')-[:' . EntityManager::PROPOSITION . ' {accept:true}]->(e:' . EntityManager::EMPLOYE . ') WHERE id(e)=$idE RETURN id(o) as id'))
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
     * @param EntityManagerInterface $em
     * @param int $idEmployeur
     * @return OffreEmploi[]
     */
    public function getMyAcceptedPropositions(EntityManagerInterface $em, int $idEmployeur): array
    {
        $results = (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYEUR . ')--(o:' . EntityManager::OFFRE_EMPLOI . ')-[:' . EntityManager::PROPOSITION . ' {accept:true}]->(c:' . EntityManager::EMPLOYE . ') WHERE id(e)=$idE RETURN id(o) as idO, id(c) as idC'))
            ->setInteger('idE', $idEmployeur)
            ->run()
            ->getResult();

        $res = [];
        foreach ($results as $result) {
            $employe = $em->getRepository(Employe::class)->findOneBy(['identity' => $result['idC']]);
            $cv = $employe->getCV();
            if (!is_null($cv)) {
                if (!is_null($cv->getEmploye())) {
                    $cv->setEmploye(null);
                }
                foreach ($cv->getCompetences() as $competence) {
                    $competence->setCV(null);
                }
                foreach ($cv->getMetiers() as $metier) {
                    $metier->setCV(null);
                }
                foreach ($cv->getDiplomes() as $diplome) {
                    $diplome->setCV(null);
                }
                foreach ($cv->getLangues() as $langue) {
                    $langue->setCV(null);
                }
            }

            $res[] = [
                'offre' => $em->getRepository(OffreEmploi::class)->findOneBy(['identity' => $result['idO']]),
                'employe' => $employe,
                'cv' => $cv,
            ];
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
     * @return string
     */
    public function getType(int $idOffre): string
    {
        $result = (new PreparedQuery('MATCH (o:' . EntityManager::OFFRE_EMPLOI . ')--(t:' . EntityManager::TYPE_CONTRAT . ') WHERE id(o)=$idO RETURN t'))
            ->setInteger('idO', $idOffre)
            ->run()
            ->getOneOrNullResult();

        return $result['t']['nom'];
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
     * @param string $metier
     * @param string $secteur
     * @return OffreEmploi[]
     */
    public function findOffreEmploiByTypeContratMetierSecteurActiviteFromPreResult(array $preResult, string $typeContrat, string $metier, string $secteur): array
    {
        $res = [];

        foreach ($preResult as $result) {
            $contratB = $typeContrat == 'none' ||
                (new PreparedQuery('MATCH (o:' . EntityManager::OFFRE_EMPLOI . ')--(t:' . EntityManager::TYPE_CONTRAT . ' {nom:$nom}) WHERE id(o)=$id RETURN id(o) AS id'))
                    ->setString('nom', $typeContrat)
                    ->setInteger('id', $result->getIdentity())
                    ->run()
                    ->getOneOrNullResult() != null;

            $metierB = $metier == 'none' ||
                (new PreparedQuery('MATCH (o:' . EntityManager::OFFRE_EMPLOI . ')--(m:' . EntityManager::METIER . ' {nom:$nom}) WHERE id(o)=$id RETURN id(o) as id'))
                    ->setString('nom', $metier)
                    ->setInteger('id', $result->getIdentity())
                    ->run()
                    ->getOneOrNullResult() != null;

            if ($secteur != 'none') {
                $m = (new OffreEmploiManager())->getMetier($result->getIdentity());
                if ($m) {
                    $secteurB = (new MetierManager())->isInSecteurActivite($m, $secteur);
                } else {
                    $secteurB = true;
                }
            } else {
                $secteurB = true;
            }

            if ($contratB && $metierB && $secteurB) {
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
        $results = (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYEUR . ')--(o:' . EntityManager::OFFRE_EMPLOI . ') WHERE id(e)=$id RETURN id(o) AS id'))
            ->setInteger('id', $idEmployeur)
            ->run()
            ->getResult();

        $res = [];

        foreach ($results as $result) {
            $res[] = $em->getRepository(OffreEmploi::class)->findOneBy(['identity' => $result['id']]);
        }

        return $res;
    }

    /**
     * @param int $idEmployeur
     * @param int $idOffre
     * @return bool
     */
    public function isOwner(int $idEmployeur, int $idOffre): bool
    {
        return (new PreparedQuery('MATCH (e:' . EntityManager::EMPLOYEUR . ')--(o:' . EntityManager::OFFRE_EMPLOI . ') WHERE id(o)=$idO AND id(e)=$idE RETURN e'))
                ->setInteger('idO', $idOffre)
                ->setInteger('idE', $idEmployeur)
                ->run()
                ->getOneOrNullResult() != null;
    }

    /**
     * @param OffreEmploi[] $offres
     * @return string[]
     */
    public function getTypes(array $offres): array
    {
        $res = [];

        foreach ($offres as $offre) {
            $res[] = $this->getType($offre->getIdentity());
        }

        return $res;
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $id
     * @return Employeur|null
     */
    public function getOwner(EntityManagerInterface $em, int $id): ?Employeur
    {
        $result = (new PreparedQuery('MATCH (o:' . EntityManager::OFFRE_EMPLOI . ')--(e:' . EntityManager::EMPLOYEUR . ') WHERE id(o)=$id RETURN id(e) AS id'))
            ->setInteger('id', $id)
            ->run()
            ->getOneOrNullResult();

        return $em->getRepository(Employeur::class)->findOneBy(['identity' => $result['id']]);
    }

    /**
     * @param int $id
     * @return string|null
     */
    public function getMetier(int $id): ?string
    {
        $result = (new PreparedQuery('MATCH (o:' . EntityManager::OFFRE_EMPLOI . ')--(m:' . EntityManager::METIER . ') WHERE id(o)=$id RETURN m.nom as nom'))
            ->setInteger('id', $id)
            ->run()
            ->getOneOrNullResult();

        return $result != null ? $result['nom'] : null;
    }
}