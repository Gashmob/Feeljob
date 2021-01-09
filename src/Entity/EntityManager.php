<?php


namespace App\Entity;


use App\database\PreparedQuery;

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

    /* -- Example --

    public static function getUserFromUsername(string $username)
    {
        return (new PreparedQuery('MATCH (u:User {name:$name}) RETURN u'))
            ->setString('name', $username)
            ->getOneOrNullResult();
    }

    */
}