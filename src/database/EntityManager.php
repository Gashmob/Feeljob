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
        $result = (new PreparedQuery('MATCH (u) WHERE u.mail=$mail RETURN u'))
            ->setString('mail', $mail)
            ->run()
            ->getOneOrNullResult();

        return $result == null ? null : new GenericUser(
            $result['u']['mail'],
            $result['u']['verifie'],
            $result['u']['motdepasse'],
            $result['u']['sel'],
            $result['u']['id']
        );
    }

    /**
     * @param int $id
     * @return string
     */
    public static function getUserTypeFromId(int $id): string
    {
        $result = (new PreparedQuery('MATCH (u) WHERE u.id=$id RETURN LABELS(u) as label'))
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
        $em->persist($candidat);
        $em->flush();

        (new PreparedQuery('CREATE (:Candidat {id:$id, email:$email, verifie:$verifie, motdepasse:$motdepasse, sel:$sel})'))
            ->setInteger('id', $candidat->getId())
            ->setString('email', $email)
            ->setBoolean('verifie', false)
            ->setString('motdepasse', $motdepasse)
            ->setString('sel', $sel)
            ->run();
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
        $em->persist($entreprise);
        $em->flush();

        (new PreparedQuery('CREATE (:Candidat {id:$id, email:$email, verifie:$verifie, motdepasse:$motdepasse, sel:$sel})'))
            ->setInteger('id', $entreprise->getId())
            ->setString('email', $email)
            ->setBoolean('verifie', false)
            ->setString('motdepasse', $motdepasse)
            ->setString('sel', $sel)
            ->run();

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
        $em->persist($autoEntrepreneur);
        $em->flush();

        (new PreparedQuery('CREATE (:Candidat {id:$id, email:$email, verifie:$verifie, motdepasse:$motdepasse, sel:$sel})'))
            ->setInteger('id', $autoEntrepreneur->getId())
            ->setString('email', $email)
            ->setBoolean('verifie', false)
            ->setString('motdepasse', $motdepasse)
            ->setString('sel', $sel)
            ->run();

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

        $result = (new PreparedQuery('MATCH (u) WHERE u.id=$id RETURN u'))
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
        $type = EntityManager::getUserTypeFromId($id);

        $user = null;
        switch ($type) {
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