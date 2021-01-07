<?php

namespace App\Tests\database;

use App\database\PreparedQuery;
use App\database\Query;
use PHPUnit\Framework\TestCase;

class PreparedQueryTest extends TestCase
{

    public function testSetInteger()
    {
        self::assertNotNull((new PreparedQuery('MATCH (u:User {name:$name}'))->setString('name', 'Fred'));
    }

    public function testSetBoolean()
    {
        self::assertNotNull((new PreparedQuery('MATCH (u:User {admin:$admin}'))->setBoolean('admin', true));
    }

    public function testSetString()
    {
        self::assertNotNull((new PreparedQuery('MATCH (u:User {age:$age}'))->setInteger('age', 20));
    }

    public function testRun()
    {

    }
}
