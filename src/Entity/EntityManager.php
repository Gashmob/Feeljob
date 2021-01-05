<?php


namespace App\Entity;


use App\database\Connection;

/**
 * Class EntityManager
 * @package App\Entity
 */
class EntityManager
{
    /**
     * @var Connection
     */
    private Connection $connection;

    /**
     * EntityManager constructor.
     */
    public function __construct()
    {
        $this->connection = new Connection();
    }

    /* _.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-.
    -- Example --

    public function getUserFromUsername(string $username)
    {
        return (new PreparedQuery('MATCH (u:User {name:$name}) RETURN u'))
            ->setString('name', $username)
            ->getOneOrNullResult();
    }

    */
}