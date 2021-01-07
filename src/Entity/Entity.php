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
    private int $id;

    /**
     * Entity constructor.
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Push the modification to the database
     */
    public abstract function flush(): void;

    /**
     * Erase the entity from the database
     */
    public abstract function erase(): void;
}