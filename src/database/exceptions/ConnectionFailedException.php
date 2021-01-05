<?php


namespace App\database\exceptions;


use Exception;

class ConnectionFailedException extends Exception
{
    /**
     * ConnectionFailedException constructor.
     */
    public function __construct()
    {
        parent::__construct('Connection failed due to wrong login or password or to bad internet connection');
    }
}