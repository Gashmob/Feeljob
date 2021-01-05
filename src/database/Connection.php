<?php


namespace App\database;


use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Contracts\ClientInterface;

class Connection
{
    /**
     * @var ClientInterface
     */
    private $connection;

    public function __construct()
    {
        $this->connection = ClientBuilder::create()
            ->addBoltConnection('default', 'neo4j://feal:feal@localhost:7687')
            ->setDefaultConnection('default')
            ->build();
    }

    /**
     * @return ClientInterface
     */
    public function getConnection(): ClientInterface
    {
        return $this->connection;
    }
}