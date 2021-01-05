<?php


namespace App\Entity;


/**
 * Class EntityManager
 * @package App\Entity
 */
abstract class EntityManager
{
    /* -- Example --

    public function getUserFromUsername(string $username)
    {
        return (new PreparedQuery('MATCH (u:User {name:$name}) RETURN u'))
            ->setString('name', $username)
            ->getOneOrNullResult();
    }

    */
}