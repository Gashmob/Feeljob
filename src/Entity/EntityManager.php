<?php


namespace App\Entity;


use App\database\PreparedQuery;
use App\database\Query;

/**
 * Class EntityManager
 * @package App\Entity
 */
abstract class EntityManager
{
    /**
     * @param string $mail
     * @return bool
     */
    public static function isMailUsed(string $mail): bool
    {
        return count((new PreparedQuery('MATCH (u {mail:$mail}) RETURN u'))
                ->setString('mail', $mail)
                ->getResult()) > 0;
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
        $result = (new PreparedQuery('MATCH (u) WHERE ID(u) = $id RETURN u'))
            ->setInteger('id', $id)
            ->run()
            ->getOneOrNullResult();

        return $result == null ? null : new GenericUser(
            $result['u']['prenom'],
            $result['u']['nom'],
            $result['u']['mail'],
            $result['u']['verification'],
            $result['u']['motdepasse'],
            $result['u']['sel'],
            $id
        );
    }

    /**
     * @param string $mail
     * @return GenericUser|null
     */
    public static function getGenericUserFromMail(string $mail): ?GenericUser
    {
        $result = (new PreparedQuery('MATCH (u) WHERE u.mail=$mail RETURN u, ID(u) as id'))
            ->setString('mail', $mail)
            ->run()
            ->getOneOrNullResult();

        return $result == null ? null : new GenericUser(
            $result['u']['prenom'],
            $result['u']['nom'],
            $result['u']['mail'],
            $result['u']['verification'],
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

    /* -- Example --

    public static function getUserFromUsername(string $username)
    {
        return (new PreparedQuery('MATCH (u:User {name:$name}) RETURN u'))
            ->setString('name', $username)
            ->run()
            ->getOneOrNullResult();
    }

    */
}