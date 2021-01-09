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