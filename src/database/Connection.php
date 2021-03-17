<?php


namespace App\database;


use App\database\exceptions\ConnectionFailedException;
use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Contracts\ClientInterface;

class Connection
{
    /**
     * @var ClientInterface
     */
    private ClientInterface $connection;

    /**
     * Connection constructor.
     * @throws ConnectionFailedException
     */
    public function __construct()
    {
        $this->connection = ClientBuilder::create()
            ->addBoltConnection('default', 'neo4j://feel:feel@localhost:7687')
            ->setDefaultConnection('default')
            ->build();

        if ($this->connection === null) throw new ConnectionFailedException();
    }

    /**
     * @return ClientInterface
     */
    public function getConnection(): ClientInterface
    {
        return $this->connection;
    }
}