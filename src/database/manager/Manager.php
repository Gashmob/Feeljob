<?php


namespace App\database\manager;


/**
 * Class Manager
 * @package App\database\repository
 */
abstract class Manager
{
    /**
     * @param int $id
     * @return string|null
     */
    public abstract function find(int $id) : ?string;

    /**
     * @param array $filters
     * @return string|null
     */
    public abstract function findOneBy(array $filters) : ?string;

    /**
     * @return array
     */
    public abstract function findAll() : array;

    /**
     * @param array $filters
     * @return array
     */
    public abstract function findBy(array $filters) : array;
}