<?php

namespace App\Tests\database;

use App\database\Connection;
use PHPUnit\Framework\TestCase;

class ConnectionTest extends TestCase
{

    public function testGetConnection()
    {
        $conn = new Connection();
        self::assertNotNull($conn);
    }

    public function test__construct()
    {
        $conn = new Connection();
        self::assertNotNull($conn->getConnection());
    }
}
