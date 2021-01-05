<?php


namespace App\Entity;


/**
 * Class Entity
 * To extends to entities of the database
 * @package App\Entity
 */
abstract class Entity
{
    /**
     * @var int
     */
    protected int $id;

    /**
     * Push the modification to the database
     */
    public abstract function flush(): void;

    /**
     * Erase the entity from the database
     */
    public abstract function erase(): void;
}