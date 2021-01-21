<?php


namespace App\database\exceptions;


use Exception;

class UserNotFoundException extends Exception
{
    /**
     * UserNotFoundException constructor.
     */
    public function __construct()
    {
        parent::__construct('User not found, please retry later');
    }
}