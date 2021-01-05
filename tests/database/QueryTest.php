<?php

namespace App\Tests\database;

use App\database\Query;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{

    public function test__construct()
    {
        $query = new Query('MATCH (n) RETURN n');
        self::assertNotNull($query);
    }

    public function testRun()
    {
        $query = new Query('MATCH (n) RETURN n');
        self::assertNotNull($query->run());
    }

    public function testGetResult()
    {
        $result = (new Query('MATCH (n) RETURN n'))->run()->getResult();
        self::assertIsArray($result);
    }

    public function testGetOneOrNullResult()
    {
        $result = (new Query('MATCH (n)'))->run()->getOneOrNullResult();
        self::assertNull($result);
    }
}
