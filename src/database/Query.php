<?php


namespace App\database;


/**
 * Class Query
 * Usage :
 * $result = (new Query('MATCH (u:User {name:"Fred"}) RETURN u'))->run()->getResult();
 * or
 * $result = (new Query('MATCH (u:User {name:"Fred"}) RETURN u'))->run()->getOneOrNullResult();
 * @package App\database
 */
class Query
{
    /**
     * @var string
     */
    protected string $query;
    /**
     * @var array
     */
    protected array $result = [];
    /**
     * @var Connection
     */
    protected Connection $connection;

    /**
     * Query constructor.
     * @param $query string
     */
    public function __construct(string $query)
    {
        $this->query = $query;
        $this->connection = new Connection();
    }

    /**
     * Run the query
     * @return $this
     */
    public function run(): Query
    {
        $results = $this->connection->getConnection()->run($this->query);
        $this->result = $results->toArray();

        return $this;
    }

    /**
     * Return the result of the query
     * @return array
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * Return the first result of the query or null if there is no result
     * @return null|mixed
     */
    public function getOneOrNullResult()
    {
        return $this->result[0];
    }
}