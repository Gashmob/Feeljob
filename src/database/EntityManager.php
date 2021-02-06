<?php


namespace App\database;


use App\database\entity\CV;
use App\database\entity\GenericUser;
use App\database\exceptions\UserNotFoundException;
use App\Entity\AutoEntrepreneur;
use App\Entity\Candidat;
use App\Entity\Entreprise;
use App\Entity\OffreEmploi;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class EntityManager
 * @package App\database
 */
abstract class EntityManager
{
    /**
     * @param string $mail
     * @return GenericUser|null
     */
    public static function getGenericUserFromMail(string $mail): ?GenericUser
    {
        $result = (new PreparedQuery('MATCH (u) WHERE u.email=$mail RETURN u, ID(u) AS id'))
            ->setString('mail', $mail)
            ->run()
            ->getOneOrNullResult();

        return $result == null ? null : new GenericUser(
            $result['u']['email'],
            $result['u']['verifie'],
            $result['u']['motdepasse'],
            $result['u']['sel'],
            $result['id']
        );
    }

    /**
     * @param int $id
     * @return string
     */
    public static function getUserTypeFromId(int $id): string
    {
        $result = (new PreparedQuery('MATCH (u) WHERE ID(u)=$id RETURN LABELS(u) as label'))
            ->setInteger('id', $id)
            ->run()
            ->getOneOrNullResult();

        return $result['label'][0];
    }

    /**
     * @param $mail
     * @return bool
     */
    public static function isMailUsed($mail): bool
    {
        return (new PreparedQuery('MATCH (u {email:$mail}) RETURN u'))
                ->setString('mail', $mail)
                ->run()
                ->getOneOrNullResult() != null;
    }

    /**
     * @param Candidat $candidat
     * @param EntityManagerInterface $em
     * @param string $motdepasse
     * @param string $sel
     * @param string $email
     */
    public static function createCandidat(Candidat $candidat, EntityManagerInterface $em, string $motdepasse, string $sel, string $email)
    {
        $result = (new PreparedQuery('CREATE (c:Candidat {email:$email, verifie:$verifie, motdepasse:$motdepasse, sel:$sel}) RETURN ID(c) AS id'))
            ->setString('email', $email)
            ->setBoolean('verifie', false)
            ->setString('motdepasse', $motdepasse)
            ->setString('sel', $sel)
            ->run()
            ->getOneOrNullResult();

        $candidat->setIdentity($result['id']);
        $em->persist($candidat);
        $em->flush();
    }

    /**
     * @param Entreprise $entreprise
     * @param EntityManagerInterface $em
     * @param string $motdepasse
     * @param string $sel
     * @param string $email
     * @param array $activites
     */
    public static function createEntreprise(Entreprise $entreprise, EntityManagerInterface $em, string $motdepasse, string $sel, string $email, array $activites)
    {
        $result = (new PreparedQuery('CREATE (e:Entreprise {email:$email, verifie:$verifie, motdepasse:$motdepasse, sel:$sel}) RETURN ID(e) AS id'))
            ->setString('email', $email)
            ->setBoolean('verifie', false)
            ->setString('motdepasse', $motdepasse)
            ->setString('sel', $sel)
            ->run()
            ->getOneOrNullResult();
        $id = $result['id'];

        foreach ($activites as $activite) {
            if ((new PreparedQuery('MATCH (s:SecteurActivite {nom:$nom}) RETURN s'))
                    ->setString('nom', $activite)
                    ->run()
                    ->getOneOrNullResult() != null) {
                (new PreparedQuery('MATCH (s:SecteurActivite {nom:$nom}), (e:Entreprise {id:$id}) CREATE (e)-[:estDans]->(s)'))
                    ->setString('nom', $activite)
                    ->setInteger('id', $id)
                    ->run();
            } else {
                (new PreparedQuery('MATCH (e:Entreprise {id:$id}) CREATE (e)-[:estDans]->(:SecteurActivite {nom:$nom})'))
                    ->setString('nom', $activite)
                    ->setInteger('id', $id)
                    ->run();
            }
        }

        $entreprise->setIdentity($id);
        $em->persist($entreprise);
        $em->flush();
    }

    /**
     * @param AutoEntrepreneur $autoEntrepreneur
     * @param EntityManagerInterface $em
     * @param string $motdepasse
     * @param string $sel
     * @param string $email
     * @param string $activite
     */
    public static function createAutoEntrepreneur(AutoEntrepreneur $autoEntrepreneur, EntityManagerInterface $em, string $motdepasse, string $sel, string $email, string $activite)
    {
        $result = (new PreparedQuery('CREATE (a:AutoEntrepreneur {email:$email, verifie:$verifie, motdepasse:$motdepasse, sel:$sel}) RETURN ID(a) AS id'))
            ->setString('email', $email)
            ->setBoolean('verifie', false)
            ->setString('motdepasse', $motdepasse)
            ->setString('sel', $sel)
            ->run()
            ->getOneOrNullResult();
        $id = $result['id'];

        if ((new PreparedQuery('MATCH (s:SecteurActivite {nom:$nom}) RETURN s'))
                ->setString('nom', $activite)
                ->run()
                ->getOneOrNullResult() != null) {
            (new PreparedQuery('MATCH (s:SecteurActivite {nom:$nom}), (e:Entreprise {id:$id}) CREATE (e)-[:estDans]->(s)'))
                ->setString('nom', $activite)
                ->setInteger('id', $id)
                ->run();
        } else {
            (new PreparedQuery('MATCH (e:Entreprise {id:$id}) CREATE (e)-[:estDans]->(:SecteurActivite {nom:$nom})'))
                ->setString('nom', $activite)
                ->setInteger('id', $id)
                ->run();
        }

        $autoEntrepreneur->setIdentity($id);
        $em->persist($autoEntrepreneur);
        $em->flush();
    }

    /**
     * @return array
     */
    public static function getAllActivitySectorName(): array
    {
        $res = [];
        $results = (new Query('MATCH (s:SecteurActivite) RETURN s'))->run()->getResult();
        foreach ($results as $result) {
            $res[] = $result['s']['nom'];
        }

        return $res;
    }

    /**
     * @param int $id
     * @return GenericUser|null
     */
    public static function getGenericUserFromId(int $id): ?GenericUser
    {

        $result = (new PreparedQuery('MATCH (u) WHERE ID(u)=$id RETURN u'))
            ->setInteger('id', $id)
            ->run()
            ->getOneOrNullResult();
        return $result == null ? null : new GenericUser(
            $result['u']['email'],
            $result['u']['verifie'],
            $result['u']['motdepasse'],
            $result['u']['sel'],
            $id
        );
    }

    /**
     * @param int $id
     * @param EntityManagerInterface $em
     * @return array
     * @throws UserNotFoundException
     */
    public static function getNomPrenomFromId(int $id, EntityManagerInterface $em): array
    {
        $user = null;
        switch (EntityManager::getUserTypeFromId($id)) {
            case 'Candidat':
                $user = $em->getRepository(Candidat::class)->findOneBy(['identity' => $id]);
                break;

            case 'Entreprise':
                $user = $em->getRepository(Entreprise::class)->findOneBy(['identity' => $id]);
                break;

            case 'AutoEntrepreneur':
                $user = $em->getRepository(AutoEntrepreneur::class)->findOneBy(['identity' => $id]);
                break;

            default:
                throw new UserNotFoundException();
        }

        if (is_null($user)) throw new UserNotFoundException();

        return ['nom' => $user->getNom(), 'prenom' => $user->getPrenom()];
    }

    /**
     * @return array
     */
    public static function getAllTypeContratName(): array
    {
        $res = [];
        $results = (new Query('MATCH (t:TypeContrat) RETURN t'))->run()->getResult();
        foreach ($results as $result) {
            $res[] = $result['t']['nom'];
        }

        return $res;
    }

    /**
     * @param OffreEmploi $offreEmploi
     * @param EntityManagerInterface $em
     * @param string $typeContrat
     * @param int $idEntreprise
     */
    public static function createOffreEmploi(OffreEmploi $offreEmploi, EntityManagerInterface $em, string $typeContrat, int $idEntreprise)
    {
        $result = (new PreparedQuery('MATCH (t:TypeContrat {nom:$nom}), (e:Entreprise) WHERE id(e)=$id CREATE (e)-[:Publie]->(o:OffreEmploi)-[:Type]->(t) RETURN id(o) AS id'))
            ->setString('nom', $typeContrat)
            ->setInteger('id', $idEntreprise)
            ->run()
            ->getOneOrNullResult();

        $offreEmploi->setIdentity($result['id']);
        $em->persist($offreEmploi);
        $em->flush();
    }

    /**
     * @return array
     */
    public static function getAllSituationFamilleName(): array
    {
        $res = [];
        $results = (new Query('MATCH (f:Famille) RETURN f'))->run()->getResult();
        foreach ($results as $result) {
            $res[] = $result['f']['nom'];
        }

        return $res;
    }

    /**
     * @return array
     */
    public static function getAllLangueName(): array
    {
        $res = [];
        $results = (new Query('MATCH (l:Langue) RETURN l'))->run()->getResult();
        foreach ($results as $result) {
            $res[] = $result['l']['nom'];
        }

        return $res;
    }

    /**
     * @return array
     */
    public static function getAllDeplacementName(): array
    {
        $res = [];
        $results = (new Query('MATCH (d:Deplacement) RETURN d'))->run()->getResult();
        foreach ($results as $result) {
            $res[] = $result['d']['nom'];
        }

        return $res;
    }

    /**
     * @return array
     */
    public static function getAllMetierName(): array
    {
        $res = [];
        $results = (new Query('MATCH (m:Metier) RETURN m'))->run()->getResult();
        foreach ($results as $result) {
            $res[] = $result['m']['nom'];
        }

        return $res;
    }

    /**
     * @return array
     */
    public static function getAllExperienceName(): array
    {
        $res = [];
        $results = (new Query('MATCH (e:Experience) RETURN e'))->run()->getResult();
        foreach ($results as $result) {
            $res[] = $result['e']['nom'];
        }

        return $res;
    }

    /**
     * @param CV $cv
     * @param string $metier
     * @param string $famille
     * @param array $diplomes
     * @param array $dates
     * @param array $nomEntreprises
     * @param array $postes
     * @param array $durees
     * @param array $langues
     * @param array $deplacements
     * @param string $typeContrat
     * @param int $idUser
     */
    public static function createCV(CV $cv, string $metier, string $famille, array $diplomes, array $dates, array $nomEntreprises, array $postes, array $durees, array $langues, array $deplacements, string $typeContrat, int $idUser)
    {
        $cv->flush();

        // Relation Candidat
        (new PreparedQuery('MATCH (c:CV), (ca:Candidat) WHERE id(c)=$id AND id(ca)=$idUser CREATE (ca)-[:Cree]->(c)'))
            ->setInteger('id', $cv->getId())
            ->setInteger('idUser', $idUser)
            ->run();

        // Relations metier et famille
        (new PreparedQuery('MATCH (c:CV), (m:Metier {nom:$metier}), (f:Famille {nom:$famille}) WHERE id(c)=$id CREATE (m)<-[:Est]-(c)-[:EstFamille]->(f)'))
            ->setString('metier', $metier)
            ->setString('famille', $famille)
            ->setInteger('id', $cv->getId())
            ->run();

        // Relations diplomes, date
        for ($i = 0; $i < sizeof($diplomes); $i++) {
            if ((new PreparedQuery('MATCH (d:Diplome {nom:$nom}) RETURN d'))
                    ->setString('nom', $diplomes[$i])
                    ->run()->getOneOrNullResult() != null) {
                (new PreparedQuery('MATCH (c:CV), (d:Diplome {nom:$nom}) WHERE id(c)=$id CREATE (c)-[:Obtenue {date:$date}]->(d)'))
                    ->setString('nom', $diplomes[$i])
                    ->setInteger('id', $cv->getId())
                    ->setString('date', $dates[$i])
                    ->run();
            } else {
                (new PreparedQuery('MATCH (c:CV) WHERE id(c)=$id CREATE (c)-[:Obtenue {date:$date}]->(:Diplome {nom:$nom})'))
                    ->setInteger('id', $cv->getId())
                    ->setString('date', $dates[$i])
                    ->setString('nom', $diplomes[$i])
                    ->run();
            }
        }

        // Relations expériences, nomEntreprise, poste, durée
        for ($i = 0; $i < sizeof($nomEntreprises); $i++) {
            if ((new PreparedQuery('MATCH (e:Experience {nom:$nom}) RETURN e'))
                    ->setString('nom', $nomEntreprises[$i])
                    ->run()->getOneOrNullResult() != null) {
                (new PreparedQuery('MATCH (c:CV), (e:Experience {nom:$nom}) WHERE id(c)=$id CREATE (c)-[:ATravaille {poste:$poste, duree:$duree}]->(e)'))
                    ->setString('nom', $nomEntreprises[$i])
                    ->setInteger('id', $cv->getId())
                    ->setString('poste', $postes[$i])
                    ->setString('duree', $durees[$i])
                    ->run();
            } else {
                (new PreparedQuery('MATCH (c:CV) WHERE id(c)=$id CREATE (c)-[:ATravaille {poste:$poste, duree:$duree}]->(:Experience {nom:$nom})'))
                    ->setInteger('id', $cv->getId())
                    ->setString('poste', $postes[$i])
                    ->setString('duree', $durees[$i])
                    ->setString('nom', $nomEntreprises[$i])
                    ->run();
            }
        }

        // Relations langues
        foreach ($langues as $langue) {
            if ((new PreparedQuery('MATCH (l:Langue {nom:$nom}) RETURN l'))
                    ->setString('nom', $langue)
                    ->run()->getOneOrNullResult() != null) {
                (new PreparedQuery('MATCH (l:Langue {nom:$nom}), (c:CV) WHERE id(c)=$id CREATE (c)-[:Parle]->(l)'))
                    ->setString('nom', $langue)
                    ->setInteger('id', $cv->getId())
                    ->run();
            } else {
                (new PreparedQuery('MATCH (c:CV) WHERE id(c)=$id CREATE (c)-[:Parle]->(:Langue {nom:$nom})'))
                    ->setInteger('id', $cv->getId())
                    ->setString('nom', $langue)
                    ->run();
            }
        }

        // Relations deplacements
        foreach ($deplacements as $deplacement) {
            if ((new PreparedQuery('MATCH (d:Deplacement {nom:$nom}) RETURN d'))
                    ->setString('nom', $deplacement)
                    ->run()->getOneOrNullResult() != null) {
                (new PreparedQuery('MATCH (c:CV), (d:Deplacement {nom:$nom}) WHERE id(c)=$id CREATE (c)-[:Utilise]->(d)'))
                    ->setString('nom', $deplacement)
                    ->setInteger('id', $cv->getId())
                    ->run();
            } else {
                (new PreparedQuery('MATCH (c:CV) WHERE id(c)=$id CREATE (c)-[:Utilise]->(:Deplacement {nom:$nom})'))
                    ->setInteger('id', $cv->getId())
                    ->setString('nom', $deplacement)
                    ->run();
            }
        }

        // Relation type contrat
        (new PreparedQuery('MATCH (c:CV), (t:TypeContrat {nom:$nom}) WHERE id(c)=$id CREATE (c)-[:Recherche]->(t)'))
            ->setString('nom', $typeContrat)
            ->setInteger('id', $cv->getId())
            ->run();
    }

    /**
     * @param int $id
     * @param EntityManagerInterface $em
     * @return array
     */
    public static function getCVArrayFromId(int $id, EntityManagerInterface $em): array
    {
        $res = [];

        // Données génériques
        $cv = (new PreparedQuery('MATCH (c:CV) WHERE id(c)=$id RETURN c'))
            ->setInteger('id', $id)
            ->run()
            ->getOneOrNullResult();
        $res['intitule'] = $cv['c']['nom'];
        $res['photo'] = $cv['c']['photo'];

        // Données candidat
        $user = (new PreparedQuery('MATCH (c:CV)--(ca:Candidat) WHERE id(c)=$id RETURN ca, id(ca) AS id'))
            ->setInteger('id', $id)
            ->run()
            ->getOneOrNullResult();
        $res['email'] = $user['ca']['email'];
        $candidat = $em->getRepository(Candidat::class)->findOneBy(['identity' => $user['id']]);
        $res['nom'] = $candidat->getNom();
        $res['prenom'] = $candidat->getPrenom();
        $res['permis'] = $candidat->getPermis();
        $res['telephone'] = $candidat->getTelephone();
        $res['naissance'] = $candidat->getNaissance();

        // Donnée métier
        $metier = (new PreparedQuery('MATCH (c:CV)--(m:Metier) WHERE id(c)=$id RETURN m'))
            ->setInteger('id', $id)
            ->run()
            ->getOneOrNullResult();
        $res['metier'] = $metier['m']['nom'];

        // Donnée famille
        $famille = (new PreparedQuery('MATCH (c:CV)--(f:Famille) WHERE id(c)=$id RETURN f'))
            ->setInteger('id', $id)
            ->run()
            ->getOneOrNullResult();
        $res['famille'] = $famille['f']['nom'];

        // Données diplomes
        $diplomes = (new PreparedQuery('MATCH (c:CV)-[r]-(d:Diplome) WHERE id(c)=$id RETURN r, d'))
            ->setInteger('id', $id)
            ->run()
            ->getResult();
        $d = [];
        for ($i = 0; $i < sizeof($diplomes); $i++) {
            $diplome['nom'] = $diplomes[$i]['d']['nom'];
            $diplome['date'] = $diplomes[$i]['r']['date'];

            $d[] = $diplome;
        }
        $res['diplomes'] = $d;

        // Données expériences
        $experiences = (new PreparedQuery('MATCH (c:CV)-[r]-(e:Experience) WHERE id(c)=$id RETURN r, e'))
            ->setInteger('id', $id)
            ->run()
            ->getResult();
        $e = [];
        for ($i = 0; $i < sizeof($experiences); $i++) {
            $experience['nomEntreprise'] = $experiences[$i]['e']['nom'];
            $experience['duree'] = $experiences[$i]['r']['duree'];
            $experience['poste'] = $experiences[$i]['r']['poste'];

            $e[] = $experience;
        }
        $res['experiences'] = $e;

        // Données langues
        $langues = (new PreparedQuery('MATCH (c:CV)--(l:Langue) WHERE id(c)=$id RETURN l'))
            ->setInteger('id', $id)
            ->run()
            ->getResult();
        $l = [];
        foreach ($langues as $langue) {
            $l[] = $langue['l']['nom'];
        }
        $res['langues'] = $l;

        // Données déplacements
        $deplacements = (new PreparedQuery('MATCH (c:CV)--(d:Deplacement) WHERE id(c)=$id RETURN d'))
            ->setInteger('id', $id)
            ->run()
            ->getResult();
        $d = [];
        foreach ($deplacements as $deplacement) {
            $d[] = $deplacement['d']['nom'];
        }
        $res['deplacement'] = $d;

        // Donnée contrat
        $contrat = (new PreparedQuery('MATCH (c:CV)--(t:TypeContrat) WHERE id(c)=$id RETURN t'))
            ->setInteger('id', $id)
            ->run()
            ->getOneOrNullResult();
        $res['contrat'] = $contrat['t']['nom'];

        return $res;
    }

    /**
     * @param int $id
     * @param EntityManagerInterface $em
     * @return string|null
     */
    public static function getUserPhoneFromId(int $id, EntityManagerInterface $em): ?string
    {
        switch (EntityManager::getUserTypeFromId($id)) {
            case 'Candidat':
                return $em->getRepository(Candidat::class)->findOneBy(['identity' => $id])->getTelephone();

            case 'Entreprise':
                return $em->getRepository(Entreprise::class)->findOneBy(['identity' => $id])->getTelephone();

            case 'AutoEntrepreneur':
                return $em->getRepository(AutoEntrepreneur::class)->findOneBy(['identity' => $id])->getTelephone();

            default:
                return null;
        }
    }

    /**
     * @param int $id
     * @param EntityManagerInterface $em
     * @return array
     */
    public static function getEmploiArrayFromId(int $id, EntityManagerInterface $em): array
    {
        $res = [];

        $result = (new PreparedQuery('MATCH (o:OffreEmploi) WHERE id(o)=$id RETURN id(o) AS id'))
            ->setInteger('id', $id)
            ->run()
            ->getOneOrNullResult();
        $identity = $result['id'];

        $offre = $em->getRepository(OffreEmploi::class)->findOneBy(['identity' => $identity]);

        $res['nom'] = $offre->getNom();
        $res['debut'] = $offre->getDebut();
        $res['fin'] = $offre->getFin();
        $res['loge'] = $offre->getLoge();
        $res['heures'] = $offre->getHeures();
        $res['salaire'] = $offre->getSalaire();
        $res['deplacement'] = $offre->getDeplacement();
        $res['lieu'] = $offre->getLieu();
        $res['teletravail'] = $offre->getTeletravail();
        $res['nbRecrutement'] = $offre->getNbRecrutement();

        return $res;
    }

    /**
     * @param EntityManagerInterface $em
     * @return mixed
     */
    public static function getAllOffreEmploi(EntityManagerInterface $em): array
    {
        $res = [];

        $result = (new PreparedQuery('MATCH (o:OffreEmploi) RETURN id(o) AS id'))
            ->run()
            ->getResult();

        foreach ($result as $id) {
            $res[] = EntityManager::getEmploiArrayFromId($id['id'], $em);
        }

        return $res;
    }

    /**
     * @param EntityManagerInterface $em
     * @param string|null $secteur
     * @param string|null $contrat
     * @param float|null $salaire
     * @param int|null $heures
     * @param bool|null $deplacement
     * @return array
     * @throws NonUniqueResultException
     */
    public static function getOffreEmploiWithFilter(EntityManagerInterface $em,
                                                    string $secteur = null, string $contrat = null,
                                                    float $salaire = null, int $heures = null,
                                                    bool $deplacement = null): array
    {
        $res = [];

        $result = (new PreparedQuery('MATCH (:SecteurActivite' . ($secteur != null ? ' {nom:$secteur}' : '') . ')--(:Entreprise)--(o:OffreEmploi)--(:TypeContrat' . ($contrat != null ? ' {nom:$contrat}' : '') . ') RETURN id(o) AS id'))
            ->setString('secteur', $secteur)
            ->setString('contrat', $contrat)
            ->run()
            ->getResult();

        foreach ($result as $id) {
            if ($em->getRepository(OffreEmploi::class)->findIdWithFiltersAndIdentity($id['id'], $salaire, $heures, $deplacement)) {
                $res[] = $res[] = EntityManager::getEmploiArrayFromId($id['id'], $em);
            }
        }

        return $res;
    }
}