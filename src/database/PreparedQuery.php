<?php


namespace App\database;


/**
 * Class PreparedQuery
 * Usage :
 * $result = (new PreparedQuery('MATCH (u:User {name:$name})'))->setString('name', 'Fred')->run()->getResult();
 * @package App\database
 */
class PreparedQuery extends Query
{
    /**
     * Array of type
     * ['key' => 'value']
     * @var array
     */
    private array $settings = [];

    /**
     * Set the $name to $value
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function setString(string $name, string $value): PreparedQuery
    {
        $this->settings[$name] = $value;

        return $this;
    }

    /**
     * Set the $name to $value
     * @param string $name
     * @param int $value
     * @return $this
     */
    public function setInteger(string $name, int $value): PreparedQuery
    {
        $this->settings[$name] = $value;

        return $this;
    }

    /**
     * Set the $name to $value
     * @param string $name
     * @param bool $value
     * @return $this
     */
    public function setBoolean(string $name, bool $value): PreparedQuery
    {
        $this->settings[$name] = $value;

        return $this;
    }

    /**
     * Run the query with parameters
     * @override
     * @return $this
     */
    public function run(): PreparedQuery
    {
        $results = $this->connection->getConnection()->run($this->query, $this->settings);
        $this->result = $results->toArray();

        return $this;
    }
}