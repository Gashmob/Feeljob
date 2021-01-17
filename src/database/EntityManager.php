<?php


namespace App\database;


use App\database\entity\GenericUser;
use App\database\exceptions\UserNotFoundException;
use App\Entity\AutoEntrepreneur;
use App\Entity\Candidat;
use App\Entity\Entreprise;
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
}