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
        $result = (new PreparedQuery('MATCH (u) WHERE u.mail=$mail RETURN u, ID(u) AS id'))
            ->setString('mail', $mail)
            ->run()
            ->getOneOrNullResult();

        return $result == null ? null : new GenericUser(
            $result['u']['mail'],
            $result['u']['verifie'],
            $result['u']['motdepasse'],
            $result['u']['sel'],
            $result['id'][0]
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
        return count((new PreparedQuery('MATCH (u {mail:$mail}) RETURN u'))
                ->setString('mail', $mail)
                ->getResult()) > 0;
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

        $candidat->setIdentity($result['id'][0]);
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

        foreach ($activites as $activite) {
            if ((new PreparedQuery('MATCH (s:SecteurActivite {nom:$nom}) RETURN s'))
                    ->setString('nom', $activite)
                    ->run()
                    ->getOneOrNullResult() != null) {
                (new PreparedQuery('MATCH (s:SecteurActivite {nom:$nom}), (e:Entreprise {id:$id}) CREATE (e)-[:estDans]->(s)'))
                    ->setString('nom', $activite)
                    ->setInteger('id', $entreprise->getId())
                    ->run();
            } else {
                (new PreparedQuery('MATCH (e:Entreprise {id:$id}) CREATE (e)-[:estDans]->(:SecteurActivite {nom:$nom})'))
                    ->setString('nom', $activite)
                    ->setInteger('id', $entreprise->getId())
                    ->run();
            }
        }

        $entreprise->setIdentity($result['id'][0]);
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

        if ((new PreparedQuery('MATCH (s:SecteurActivite {nom:$nom}) RETURN s'))
                ->setString('nom', $activite)
                ->run()
                ->getOneOrNullResult() != null) {
            (new PreparedQuery('MATCH (s:SecteurActivite {nom:$nom}), (e:Entreprise {id:$id}) CREATE (e)-[:estDans]->(s)'))
                ->setString('nom', $activite)
                ->setInteger('id', $autoEntrepreneur->getId())
                ->run();
        } else {
            (new PreparedQuery('MATCH (e:Entreprise {id:$id}) CREATE (e)-[:estDans]->(:SecteurActivite {nom:$nom})'))
                ->setString('nom', $activite)
                ->setInteger('id', $autoEntrepreneur->getId())
                ->run();
        }

        $autoEntrepreneur->setIdentity($result['id'][0]);
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
            $result['u']['mail'],
            $result['u']['verification'],
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
                $user = $em->getRepository(Candidat::class)->find($id);
                break;

            case 'Entreprise':
                $user = $em->getRepository(Entreprise::class)->find($id);
                break;

            case 'AutoEntrepreneur':
                $user = $em->getRepository(AutoEntrepreneur::class)->find($id);
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

        $offreEmploi->setIdentity($result['id'][0]);
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
     */
    public static function createCV(CV $cv, string $metier, string $famille, array $diplomes, array $dates, array $nomEntreprises, array $postes, array $durees, array $langues, array $deplacements, string $typeContrat)
    {
        $cv->flush();

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
}