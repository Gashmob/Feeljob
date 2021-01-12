<?php


namespace App\database;


use Ds\Map;

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
    private static string $graph = 'fealjob';

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
        $this->query = 'USE ' . self::$graph . ' ' . $query;
        $this->connection = new Connection();
    }

    /**
     * Run the query
     * @return $this
     */
    public function run(): Query
    {
        $results = $this->connection->getConnection()->run($this->query);
        for ($i = 0; $i < $results->count(); $i++) {
            $result = $results->get($i);
            $this->result[] = $result->toArray();
        }

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
     * @return mixed|null
     */
    public function getOneOrNullResult()
    {
        return isset($this->result[0]) ? $this->result[0] : null;
    }
}